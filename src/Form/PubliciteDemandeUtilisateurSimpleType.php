<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Jours;
use App\Entity\PubliciteDemande;
use App\Entity\Region;
use App\Entity\UtilisateurSimple;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class PubliciteDemandeUtilisateurSimpleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['type'];


        if ($type == "edit") {
            $builder
                ->add(
                    'ordre',
                    ChoiceType::class,
                    [
                        'placeholder' => 'Choisir un ordre',
                        'label' => 'Ordre',
                        'required'     => false,
                        'expanded'     => false,
                        'attr' => ['class' => 'has-select2'],
                        'multiple' => false,
                        'choices'  => array_flip([
                            '1' => '1',
                            '2' => '2',
                            '3' => '3'
                        ]),
                    ]
                )

                ->add(
                    'nature',
                    ChoiceType::class,
                    [
                        'placeholder' => 'Choisir une nature',
                        'label' => 'Ordre',
                        'required'     => false,
                        'expanded'     => false,
                        'attr' => ['class' => 'has-select2'],
                        'multiple' => false,
                        'choices'  => array_flip([
                            'Categorie' => 'Catégorie',
                            'Region' => 'Région',
                            'Encart' => 'Encart'
                        ]),
                    ]
                )
                ->add('categorie', EntityType::class, [
                    'class' => Categorie::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Selectionnez une categorie',
                    'attr' => ['class' => 'categorie form-select'],
                    'label' => "Catégorie",
                    'required' => false,

                ])
                ->add('region', EntityType::class, [
                    'class' => Region::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Selectionnez une région',
                    'attr' => ['class' => 'categorie form-select'],
                    'label' => "Région",
                    'required' => false,

                ]);
        }

        if ($type == "rejeter") {
            $builder->add('messageRejeter', TextareaType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champs message")
                )
            ]);
        } else {
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
                ])
                ->add('utilisateur', EntityType::class, [
                    'class' => UtilisateurSimple::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Selectionnez un utilisateur',
                    'attr' => ['class' => 'categorie form-select'],
                    'label' => 'Utilisateur simple',
                    'required' => true,
                    "constraints" => array(
                        new NotNull(null, "S'il vous veillez renseigner le champs utilisateur simple")
                    )
                ]);
        }
        $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-default btn-sm', 'data-bs-dismiss' => 'modal']])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-main btn-ajax btn-sm']])
            ->add('rejeter', SubmitType::class, ['label' => 'Rejeter la proposition', 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])
            ->add('passer', SubmitType::class, ['label' => 'Valider la proposition', 'attr' => ['class' => 'btn btn-success btn-ajax btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PubliciteDemande::class,
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
