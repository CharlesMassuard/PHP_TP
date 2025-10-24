<?php

namespace App\Controller;

use App\Repository\OuvrageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OuvrageController extends AbstractController
{
    #[Route('/ouvrages', name: 'app_ouvrages')]
    public function index(OuvrageRepository $ouvrage_repository): Response
    {
        return $this->render('ouvrage/index.html.twig', [
            'ouvrages' => $ouvrage_repository->findAll(),
        ]);
    }
}
