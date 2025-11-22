<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OuvrageRepository;
use App\Repository\ExemplairesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Emprunt;
use App\Entity\ReglesEmprunts;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;

final class EmpruntController extends AbstractController
{
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

    #[Route('/user/reservations/{reservationId}/annuler', name: 'annuler_reservation')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour réserver un exemplaire.')]
    public function cancelReservation(int $reservationId, EmpruntRepository $empruntRepository, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();
        $reservation = $empruntRepository->find($reservationId);
        if (!$reservation || $reservation->getUser() !== $user || $reservation->getStatut() !== 'Réservé') {
            throw $this->createNotFoundException('Réservation non trouvée ou accès refusé');
        }   
        $reservation->setStatut('Annulé');
        $entityManager->flush();

        //trouver les emprunts en attente pour le même exemplaire
        $empruntsEnAttente = $empruntRepository->createQueryBuilder('e')
            ->where('e.exemplaire = :exemplaire')
            ->andWhere('e.statut = :statut')
            ->setParameter('exemplaire', $reservation->getExemplaire())
            ->setParameter('statut', 'Réservé')
            ->getQuery()
            ->getResult();

        //si il y a des emprunts en attente, passer le premier en statut emprunté
        if (count($empruntsEnAttente) > 0) {
            $premierEmprunt = $empruntsEnAttente[0];
            $premierEmprunt->setStatut('Emprunté');
        } else {
            //sinon, marquer l'exemplaire comme disponible
            $exemplaire = $reservation->getExemplaire();
            $exemplaire->setDisponibilite(true);
        }
        $entityManager->flush();

        $this->addFlash('success', 'Réservation annulée avec succès !');
        return $this->redirectToRoute('app_user_reservations');
    }
}
