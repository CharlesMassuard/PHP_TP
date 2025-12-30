<?php

namespace App\Form;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use App\Entity\EtatExemplaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExemplaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cote')
            ->add('etat', EnumType::class, [
                'class' => EtatExemplaire::class,
                'choice_label' => fn (EtatExemplaire $choice) => match($choice) {
                    EtatExemplaire::BON => 'bon',
                    EtatExemplaire::MOYEN => 'moyen',
                    EtatExemplaire::MAUVAIS => 'mauvais'
                },
            ])
            ->add('emplacement')
            ->add('disponibilite')
            ->add('ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exemplaires::class,
        ]);
    }
}