<?php

namespace App\Form;

use App\Entity\Jours;
use App\Entity\PubliciteEncart;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class PubliciteEncartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['type'];

        $builder
            ->add('publiciteImages', CollectionType::class, [
                'entry_type' => PubliciteImageType::class,
                'entry_options' => [
                    'label' => false,
                    'doc_options' => $options['doc_options'],
                    'doc_required' => $options['doc_required'],
                    'validation_groups' => $options['validation_groups'],
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('libelle')

            ->add('dateDebut', DateType::class, [
                'label' => 'Date debut',
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto skip-init'],
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'widget' => 'single_text',
                "empty_data" => new \DateTime(),
            ])
            ->add('dateFin',  DateType::class, [
                'label' => 'Date fin',
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto skip-init'],
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),

                    new GreaterThan([
                        'propertyPath' => 'parent.all[dateDebut].data'
                    ]),
                ]
            ])

            ->add('heureDebut', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('heureFin', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('jours', EntityType::class, [
                'label'        => "Jours",
                'choice_label' => 'libelle',
                /*  'choice_attr' => function (InfoSerie $info) {
                    return ['data-value' => $info->getid()];
                }, */
                'multiple'     => true,
                'expanded'     => false,
                'placeholder' => 'Choisir des jours',
                'attr' => ['class' => 'has-select2 jours'],
                'class'        => Jours::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PubliciteEncart::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['type']);
        $resolver->setRequired(['validation_groups']);
    }
}
