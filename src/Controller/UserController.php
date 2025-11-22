<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Entity\Emprunt;

final class UserController extends AbstractController
{
    #[Route('/user/reservations', name: 'app_user_reservations')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour voir vos réservations.')]
    public function reservations(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view reservations.');
        }
        $reservations = $user->getEmprunts();
        
        return $this->render('user/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }
}