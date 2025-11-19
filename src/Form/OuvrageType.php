<?php

namespace App\Form;

use App\Entity\Ouvrage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OuvrageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            // { changed code - utilise les méthodes string }
            ->add('auteursAsString', TextType::class, [
                'label' => 'Auteurs (séparés par des virgules)',
                'required' => true,
                'mapped' => false,
                'data' => $options['data'] ? $options['data']->getAuteursAsString() : '',
            ])
            ->add('Editeur', TextType::class, [
                'label' => 'Éditeur',
                'required' => true,
            ])
            ->add('Annee', DateType::class, [
                'label' => 'Année de parution',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('languesAsString', TextType::class, [
                'label' => 'Langues (séparées par des virgules)',
                'required' => true,
                'mapped' => false,
                'data' => $options['data'] ? $options['data']->getLanguesAsString() : '',
            ])
            ->add('categoriesAsString', TextType::class, [
                'label' => 'Catégories (séparées par des virgules)',
                'required' => true,
                'mapped' => false,
                'data' => $options['data'] ? $options['data']->getCategoriesAsString() : '',
            ])
            ->add('tagsAsString', TextType::class, [
                'label' => 'Tags (séparés par des virgules)',
                'required' => true,
                'mapped' => false,
                'data' => $options['data'] ? $options['data']->getTagsAsString() : '',
            ])
            ->add('ISBN', TextType::class, [
                'label' => 'ISBN',
                'required' => false,
            ])
            ->add('ISSN', TextType::class, [
                'label' => 'ISSN',
                'required' => false,
            ])
            ->add('Resume', TextareaType::class, [
                'label' => 'Résumé',
                'required' => true,
                'attr' => ['rows' => 5],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ouvrage::class,
        ]);
    }
}