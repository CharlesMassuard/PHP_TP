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
use App\Service\AuditLogger;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\EmailService;

final class EmpruntController extends AbstractController
{
    #[Route('/ouvrage/{id}/exemplaires/{exemplaireId}/reserve', name: 'exemplaire_reservation')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour réserver un exemplaire.')]
    public function reserve(OuvrageRepository $ouvrage_repository, ExemplairesRepository $exemplaires_repository, int $id, int $exemplaireId, EntityManagerInterface $entityManager, AuditLogger $auditLogger, EmailService $emailService): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }

        $exemplaire = $exemplaires_repository->findExemplaireById($exemplaireId);
        if (!$exemplaire) {
            throw $this->createNotFoundException('Exemplaire non trouvé');
        }

        $categorieOuvrage = $ouvrage->getCategories()[0] ?? null;
        $reglesRepository = $entityManager->getRepository(ReglesEmprunts::class);
        $regle = $reglesRepository->findOneBy(['categorie' => $categorieOuvrage]);
        if ($regle) {
            $dureeEmprunt = $regle->getDureeEmpruntJours();
            $nbrMaxEmpruntsParCategorie = $regle->getNombreMaxEmrpunts();
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $nbrEmpruntsCatUser = $user->getEmprunts()->filter(function (Emprunt $emprunt) use ($categorieOuvrage) {
                return $emprunt->getExemplaire()->getOuvrage()->getCategories()[0] === $categorieOuvrage
                    && in_array($emprunt->getStatut(), ['Emprunté', 'Réservé']);
            })->count();
            if ($nbrEmpruntsCatUser >= $nbrMaxEmpruntsParCategorie) {
                $this->addFlash('error', 'Vous avez atteint le nombre maximum d\'emprunts pour cette catégorie.');
                return $this->redirectToRoute('app_ouvrage_exemplaires', ['id' => $id]);
            }
        } else {
            $dureeEmprunt = 14;
        }

        // Marquer l'exemplaire comme non disponible
        $exemplaire->setDisponibilite(false);
        $entityManager->flush();

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

        $auditLogger->log('RESERVATION', [
            'ouvrage_id' => $id,
            'exemplaire_id' => $exemplaireId,
            'nouvelle_reservation_id' => $emprunt->getId(),
            'statut' => $emprunt->getStatut()
        ]);

        $this->addFlash('success', $msgOk);  
        /** @var User $user */
        $user = $this->getUser();
        if ($emprunt->getStatut() === 'Réservé') {
            $emailService->sendReservationEmail(
                $user->getEmail(),
                [
                    'user' => $this->getUser(),
                    'ouvrage' => $ouvrage,
                    'exemplaire' => $exemplaire,
                    'statut' => $emprunt->getStatut(),
                    'dateRetour' => $emprunt->getDateRetour()
                ]
            );
        } elseif ($emprunt->getStatut() === 'Emprunté') {
            $emailService->sendEmpruntEmail(
                $user->getEmail(),
                [
                    'user' => $this->getUser(),
                    'ouvrage' => $ouvrage,
                    'exemplaire' => $exemplaire,
                    'statut' => $emprunt->getStatut(),
                    'dateRetour' => $emprunt->getDateRetour()
                ]
            );
        }

        return $this->redirectToRoute('app_ouvrage_exemplaires', ['id' => $id]);
    }

    #[Route('/user/reservations/{reservationId}/annuler', name: 'annuler_reservation')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour réserver un exemplaire.')]
    public function cancelReservation(int $reservationId, EmpruntRepository $empruntRepository, EntityManagerInterface $entityManager, AuditLogger $auditLogger, EmailService $emailService): Response
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
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();

        //si il y a des emprunts en attente, passer le premier en statut emprunté
        $exemplaire = $reservation->getExemplaire();
        if (count($empruntsEnAttente) > 0) {
            $premierEmprunt = $empruntsEnAttente[0];
            $premierEmprunt->setStatut('Emprunté');
            $categorieOuvrage = $premierEmprunt->getExemplaire()->getOuvrage()->getCategories()[0] ?? null;
            $reglesRepository = $entityManager->getRepository(ReglesEmprunts::class);
            $regle = $reglesRepository->findOneBy(['categorie' => $categorieOuvrage]);
            if ($regle) {
                $dureeEmprunt = $regle->getDureeEmpruntJours();
            } else {
                $dureeEmprunt = 14;
            }
            $dateRetour = new \DateTimeImmutable()->modify('+' . $dureeEmprunt . ' days');
            $premierEmprunt->setDateRetour($dateRetour);

            $nouvelEmprunteur = $premierEmprunt->getUser();
            $emailService->sendEmpruntEmail(
                $nouvelEmprunteur->getEmail(),
                [
                    'user' => $nouvelEmprunteur,
                    'ouvrage' => $premierEmprunt->getExemplaire()->getOuvrage(),
                    'exemplaire' => $premierEmprunt->getExemplaire(),
                    'statut' => "Emprunté",
                    'dateRetour' => $dateRetour
                ]
            );
        } else {
            //sinon, marquer l'exemplaire comme disponible
            $exemplaire->setDisponibilite(true);
        }
        $entityManager->flush();

        $auditLogger->log('ANNULATION', [
            'reservation_id' => $reservationId,
            'exemplaire_id' => $exemplaire->getId(),
            'ouvrage_id' => $exemplaire->getOuvrage()->getId(),
            'nouvelle_disponibilite_exemplaire' => $exemplaire->getDisponibilite() ? 'Disponible' : 'Non Disponible'
        ]);

        /** @var User $user */
        $user = $this->getUser();
        $emailService->sendCancellationEmail(
            $user->getEmail(),
            [
                'user' => $this->getUser(),
                'ouvrage' => $reservation->getExemplaire()->getOuvrage(),
                'exemplaire' => $reservation->getExemplaire(),
                'statut' => $reservation->getStatut()
            ]
        );

        $this->addFlash('success', 'Réservation annulée avec succès !');
        return $this->redirectToRoute('app_user_reservations');
    }

    #[Route('/user/reservations/{reservationId}/retourner', name: 'retourner_reservation')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour retourner un exemplaire.')]
    public function returnReservation(int $reservationId, EmpruntRepository $empruntRepository, EntityManagerInterface $entityManager, AuditLogger $auditLogger, EmailService $emailService): Response
    {   
        $user = $this->getUser();
        $reservation = $empruntRepository->find($reservationId);
        if (!$reservation || $reservation->getUser() !== $user || $reservation->getStatut() !== 'Emprunté') {
            throw $this->createNotFoundException('Emprunt non trouvé ou accès refusé');
        }   
        $reservation->setStatut('Retourné');
        $entityManager->flush();

        //trouver les emprunts en attente pour le même exemplaire
        $empruntsEnAttente = $empruntRepository->createQueryBuilder('e')
            ->where('e.exemplaire = :exemplaire')
            ->andWhere('e.statut = :statut')
            ->setParameter('exemplaire', $reservation->getExemplaire())
            ->setParameter('statut', 'Réservé')
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();

        //si il y a des emprunts en attente, passer le premier en statut emprunté
        $exemplaire = $reservation->getExemplaire();
        if (count($empruntsEnAttente) > 0) {
            $premierEmprunt = $empruntsEnAttente[0];
            $premierEmprunt->setStatut('Emprunté');
             $categorieOuvrage = $premierEmprunt->getExemplaire()->getOuvrage()->getCategories()[0] ?? null;
            $reglesRepository = $entityManager->getRepository(ReglesEmprunts::class);
            $regle = $reglesRepository->findOneBy(['categorie' => $categorieOuvrage]);
            if ($regle) {
                $dureeEmprunt = $regle->getDureeEmpruntJours();
            } else {
                $dureeEmprunt = 14;
            }
            $dateRetour = new \DateTimeImmutable()->modify('+' . $dureeEmprunt . ' days');
            $premierEmprunt->setDateRetour($dateRetour);

            $nouvelEmprunteur = $premierEmprunt->getUser();
            $emailService->sendEmpruntEmail(
                $nouvelEmprunteur->getEmail(),
                [
                    'user' => $nouvelEmprunteur,
                    'ouvrage' => $premierEmprunt->getExemplaire()->getOuvrage(),
                    'exemplaire' => $premierEmprunt->getExemplaire(),
                    'statut' => "Emprunté",
                    'dateRetour' => $dateRetour
                ]
            );
        } else {
            //sinon, marquer l'exemplaire comme disponible
            $exemplaire->setDisponibilite(true);
        }
        $entityManager->flush();

        $auditLogger->log('RETOUR', [
            'reservation_id' => $reservationId,
            'exemplaire_id' => $exemplaire->getId(),
            'ouvrage_id' => $exemplaire->getOuvrage()->getId(),
            'nouvelle_disponibilite_exemplaire' => $exemplaire->getDisponibilite() ? 'Disponible' : 'Non Disponible'
        ]);

        /** @var User $user */
        $user = $this->getUser();
        $emailService->sendReturnEmail(
            $user->getEmail(),
            [
                'user' => $this->getUser(),
                'ouvrage' => $reservation->getExemplaire()->getOuvrage(),
                'exemplaire' => $reservation->getExemplaire(),
                'statut' => $reservation->getStatut()
            ]
        );

        $this->addFlash('success', 'Exemplaire retourné avec succès !');
        return $this->redirectToRoute('app_user_reservations');
    }
}
