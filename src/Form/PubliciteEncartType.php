<?php

namespace App\Form;

use App\Entity\Jours;
use App\Entity\PubliciteEncart;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $builder
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
        ]);
    }
}
