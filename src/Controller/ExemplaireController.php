<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OuvrageRepository;
use App\Repository\ExemplairesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\ExemplaireType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Emprunt;
use App\Entity\ReglesEmprunts;

final class ExemplaireController extends AbstractController
{
    #[Route('/ouvrage/{id}/exemplaires', name: 'app_ouvrage_exemplaires')]
    public function exemplaires(OuvrageRepository $ouvrage_repository, int $id): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }

        return $this->render('exemplaire/exemplaires.html.twig', [
            'ouvrage' => $ouvrage,
            'exemplaires' => $ouvrage_repository->getExemplaires($id),
        ]);
    }

    #[Route('/ouvrage/{id}/exemplaires/new', name: 'exemplaire_new')]
    #[IsGranted('ROLE_LIBRARIAN', message: 'Vous devez être bibliothécaire pour créer un exemplaire.')]
    public function new(OuvrageRepository $ouvrage_repository, Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }
        
        $exemplaire = new \App\Entity\Exemplaires();
        $form = $this->createForm(ExemplaireType::class, $exemplaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $exemplaire->setOuvrage($ouvrage);
            $entityManager->persist($exemplaire);
            $entityManager->flush();
            $this->addFlash('success', 'Exemplaire créé avec succès !');
            return $this->redirectToRoute('app_ouvrage_exemplaires', ['id' => $id]);
        }

        return $this->render('exemplaire/exemplaire_edit.html.twig', [
            'ouvrage' => $ouvrage,
            'exemplaire' => $exemplaire,
            'form' => $form,
        ]);
    }

    #[Route('/ouvrage/{id}/exemplaires/{exemplaireId}/edit', name: 'exemplaire_edit')]
    #[IsGranted('ROLE_LIBRARIAN', message: 'Vous devez être bibliothécaire pour modifier un exemplaire.')]
    public function edit(OuvrageRepository $ouvrage_repository, ExemplairesRepository $exemplaires_repository, Request $request, int $id, int $exemplaireId, EntityManagerInterface $entityManager): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }

        $exemplaire = $exemplaires_repository->findExemplaireById($exemplaireId);
        if (!$exemplaire) {
            throw $this->createNotFoundException('Exemplaire non trouvé');
        }
        $form = $this->createForm(ExemplaireType::class, $exemplaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Exemplaire modifié avec succès !');
            return $this->redirectToRoute('app_ouvrage_exemplaires', ['id' => $id]);
        }

        return $this->render('exemplaire/exemplaire_edit.html.twig', [
            'ouvrage' => $ouvrage,
            'exemplaire' => $exemplaire,
            'form' => $form,
        ]);
    }

    #[Route('/ouvrage/{id}/exemplaires/{exemplaireId}/reserve', name: 'exemplaire_reservation')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour réserver un exemplaire.')]
    public function reserve(OuvrageRepository $ouvrage_repository, ExemplairesRepository $exemplaires_repository, int $id, int $exemplaireId, EntityManagerInterface $entityManager): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }

        $exemplaire = $exemplaires_repository->findExemplaireById($exemplaireId);
        if (!$exemplaire) {
            throw $this->createNotFoundException('Exemplaire non trouvé');
        }
        
        // Marquer l'exemplaire comme non disponible
        $exemplaire->setDisponibilite(false);
        $entityManager->flush();

        $categorieOuvrage = $ouvrage->getCategories()[0] ?? null;
        $reglesRepository = $entityManager->getRepository(ReglesEmprunts::class);
        $regle = $reglesRepository->findOneBy(['categorie' => $categorieOuvrage]);
        if ($regle) {
            $dureeEmprunt = $regle->getDureeEmpruntJours();
        } else {
            $dureeEmprunt = 14;
        }

        $emprunt = new Emprunt();
        $emprunt->setUser($this->getUser());
        $emprunt->setExemplaire($exemplaire);
        $emprunt->setDateRetour(new \DateTimeImmutable('+' . $dureeEmprunt . ' days'));

        $empruntsActifs = $entityManager->getRepository(Emprunt::class)->findBy([
            'exemplaire' => $exemplaire,
            'statut' => ['Emprunté', 'Réservé']
        ]);

        $msgOk = 'Exemplaire réservé avec succès !';

        if (count($empruntsActifs) > 0) {
            $emprunt->setStatut('Réservé');
        } else {
            $emprunt->setStatut('Emprunté');
            $msgOk = 'Exemplaire emprunté avec succès !';
        }

        $entityManager->persist($emprunt);
        $entityManager->flush();

        $this->addFlash('success', $msgOk);
        return $this->redirectToRoute('app_ouvrage_exemplaires', ['id' => $id]);
    }
}