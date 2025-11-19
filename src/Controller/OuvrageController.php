<?php

namespace App\Controller;

use App\Repository\OuvrageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\OuvrageSearchType;
use App\Form\OuvrageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ouvrage;

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

    #[Route('/ouvrage/new', name: 'app_ouvrage_new')]
    #[IsGranted('ROLE_LIBRARIAN', message: 'Vous devez être bibliothécaire pour créer un ouvrage.')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ouvrage = new Ouvrage();
        $form = $this->createForm(OuvrageType::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('auteursAsString')) {
                $ouvrage->setAuteursFromString($form->get('auteursAsString')->getData());
            }
            if ($form->has('languesAsString')) {
                $ouvrage->setLanguesFromString($form->get('languesAsString')->getData());
            }
            if ($form->has('categoriesAsString')) {
                $ouvrage->setCategoriesFromString($form->get('categoriesAsString')->getData());
            }
            if ($form->has('tagsAsString')) {
                $ouvrage->setTagsFromString($form->get('tagsAsString')->getData());
            }

            $entityManager->persist($ouvrage);
            $entityManager->flush();
            $this->addFlash('success', 'Ouvrage créé avec succès !');
            return $this->redirectToRoute('app_ouvrage_detail', ['id' => $ouvrage->getId()]);
        }

        return $this->render('ouvrage/edit.html.twig', [
            'ouvrage' => $ouvrage,
            'form' => $form,
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

    #[Route('/ouvrage/{id}/edit', name: 'app_ouvrage_edit')]
    #[IsGranted('ROLE_LIBRARIAN', message: 'Vous devez être bibliothécaire pour modifier un ouvrage.')]
    public function edit(OuvrageRepository $ouvrage_repository, Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $ouvrage = $ouvrage_repository->find($id);
        if (!$ouvrage) {
            throw $this->createNotFoundException('Ouvrage non trouvé');
        }

        $form = $this->createForm(OuvrageType::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('auteursAsString')) {
                $ouvrage->setAuteursFromString($form->get('auteursAsString')->getData());
            }
            if ($form->has('languesAsString')) {
                $ouvrage->setLanguesFromString($form->get('languesAsString')->getData());
            }
            if ($form->has('categoriesAsString')) {
                $ouvrage->setCategoriesFromString($form->get('categoriesAsString')->getData());
            }
            if ($form->has('tagsAsString')) {
                $ouvrage->setTagsFromString($form->get('tagsAsString')->getData());
            }

            $entityManager->flush();
            $this->addFlash('success', 'Ouvrage modifié avec succès !');
            return $this->redirectToRoute('app_ouvrage_detail', ['id' => $id]);
        }

        return $this->render('ouvrage/edit.html.twig', [
            'ouvrage' => $ouvrage,
            'form' => $form,
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
