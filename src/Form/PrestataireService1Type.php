<?php

namespace App\Form;

use App\Entity\PrestataireService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestataireService1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat')
            ->add('countVisite')
            ->add('dateCreation')
            ->add('prestataire')
            ->add('categorie')
            ->add('service')
            ->add('sousCategorie')
            ->add('image')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrestataireService::class,
        ]);
    }
}
