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

    #[Route('/ouvrage/{id}', name: 'app_ouvrage_detail')]
    public function detail(OuvrageRepository $ouvrage_repository, int $id): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }
        return $this->render('ouvrage/detail.html.twig', [
            'ouvrage' => $ouvrage,
            'isDisponible' => $ouvrage_repository->isDisponible($id),
        ]);
    }

    #[Route('/ouvrage/{id}/exemplaires', name: 'app_ouvrage_exemplaires')]
    public function exemplaires(OuvrageRepository $ouvrage_repository, int $id): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }

        return $this->render('ouvrage/exemplaires.html.twig', [
            'ouvrage' => $ouvrage,
            'exemplaires' => $ouvrage_repository->getExemplaires($id),
        ]);
    }
}
