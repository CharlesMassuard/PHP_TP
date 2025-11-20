<?php

namespace App\Form;

use App\Entity\ReglesEmprunts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReglesEmpruntsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie')
            ->add('dureeEmpruntJours')
            ->add('nombreMaxEmrpunts')
            ->add('penaliteParJour')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReglesEmprunts::class,
        ]);
    }
}
