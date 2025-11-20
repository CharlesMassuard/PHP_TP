<?php

namespace App\Controller;

use App\Entity\ReglesEmprunts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReglesEmpruntsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ReglesEmpruntsType;
use App\Repository\OuvrageRepository;

final class ReglesEmpruntsController extends AbstractController
{
    #[Route('/regles/emprunts', name: 'app_regles_emprunts')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être admin pour accéder aux règles d\'emprunts.')]
    public function index(ReglesEmpruntsRepository $regles_repository): Response
    {
        $regles = $regles_repository->findAll();

        return $this->render('regles_emprunts/index.html.twig', [
            'regles' => $regles,
        ]);
    }

    #[Route('/regles/emprunts/new', name: 'new_regle_emprunt')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être admin pour créer une règle d\'emprunt.')]
    public function new(Request $request, OuvrageRepository $ouvrage_repository, EntityManagerInterface $entityManager): Response
    {
        $regle = new ReglesEmprunts();
        $choicesCategories = $ouvrage_repository->findDistinctCategories();
        $form = $this->createForm(ReglesEmpruntsType::class, $regle, [
            'choices_categories' => $choicesCategories,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($regle);
            $entityManager->flush();
            $this->addFlash('success', 'Règle d\'emprunt créée avec succès !');
            return $this->redirectToRoute('app_regles_emprunts');
        }

        return $this->render('regles_emprunts/edit.html.twig', [
            'regle' => $regle,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/regles/emprunts/{id}/edit', name: 'edit_regle_emprunt')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être admin pour modifier une règle d\'emprunt.')]
    public function edit(ReglesEmpruntsRepository $regles_repository, Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $regle = $regles_repository->find($id);
        if (!$regle) {
            throw $this->createNotFoundException('Règle d\'emprunt non trouvée');
        }   
        $form = $this->createForm(ReglesEmpruntsType::class, $regle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($regle);
            $entityManager->flush();
            $this->addFlash('success', 'Règle d\'emprunt modifiée avec succès !');
            return $this->redirectToRoute('app_regles_emprunts');
        }
        return $this->render('regles_emprunts/edit.html.twig', [
            'regle' => $regle,
            'form' => $form->createView(),
        ]);
    }
}
