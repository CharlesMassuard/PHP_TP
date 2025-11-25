<?php

namespace App\Form;

use App\Entity\Ouvrage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OuvrageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', SearchType::class, [
                'required' => false,
                'label' => 'Titre',
            ])
            ->add('categories', ChoiceType::class, [
                'choices' => $options['choices_categories'] ?? [],
                'required' => false,
                'multiple' => true,
                'label' => 'Catégories',
            ])
            ->add('langues', ChoiceType::class, [
                'choices' => $options['choices_langues'] ?? [],
                'required' => false,
                'multiple' => true,
                'label' => 'Langues',
            ])
            ->add('year_from', IntegerType::class, [
                'required' => false,
                'label' => 'Année min',
            ])
            ->add('year_to', IntegerType::class, [
                'required' => false,
                'label' => 'Année max',
            ])
            ->add('disponible', ChoiceType::class, [
                'choices' => [
                    'Tous' => null,
                    'Disponible' => 'yes',
                    'Indisponible' => 'no',
                ],
                'required' => false,
                'label' => 'Disponibilité',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices_categories' => [],
            'choices_langues' => [],
            'csrf_protection' => false,
        ]);

        $resolver->setAllowedTypes('choices_categories', ['array']);
        $resolver->setAllowedTypes('choices_langues', ['array']);
    }
}
