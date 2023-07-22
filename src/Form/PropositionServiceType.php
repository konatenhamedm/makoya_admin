<?php

namespace App\Form;

use App\Entity\Prestataire;
use App\Entity\PropositionService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropositionServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('etat')
            ->add('dateCreation')
            ->add('prestataire', EntityType::class, [
                'class' => Prestataire::class,
                'choice_label' => 'denomination',
                'label' => 'Prestataire',
                'attr' => ['class' => 'has-select2 form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropositionService::class,
        ]);
    }
}
