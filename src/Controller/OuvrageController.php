<?php

namespace App\Controller;

use App\Repository\OuvrageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\OuvrageSearchType;
use Symfony\Component\HttpFoundation\Request;

final class OuvrageController extends AbstractController
{
    #[Route('', name: 'home')]
    #[Route('/ouvrages', name: 'app_ouvrages')]
    public function index(OuvrageRepository $ouvrage_repository, Request $request): Response
    {
        $choicesCategories = $ouvrage_repository->findDistinctCategories();
        $choicesLangues = $ouvrage_repository->findDistinctLangues();

        $form = $this->createForm(OuvrageSearchType::class, null, [
            'choices_categories' => $choicesCategories,
            'choices_langues' => $choicesLangues,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        $data = $form->getData() ?? [];
        $criteria = array_filter($data, fn($v) => $v !== null && $v !== '' && $v !== []);

        $ouvrages = $criteria ? $ouvrage_repository->search($criteria) : $ouvrage_repository->findAll();

        return $this->render('ouvrage/index.html.twig', [
            'ouvrages' => $ouvrages,
            'searchForm' => $form->createView(),
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
