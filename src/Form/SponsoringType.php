<?php

namespace App\Form;

use App\Entity\Quartier;
use App\Entity\Sponsoring;
use App\Entity\UserFront;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SponsoringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($options['type'] == 'allData') {
            $builder
                ->add('titre')
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
                ->add('description')
                ->add('email', EmailType::class, [])
                ->add('contact')
                ->add(
                    'image',
                    FichierType::class,
                    [
                        'label' => 'Fichier',
                        'label' => 'image',
                        'doc_options' => $options['doc_options'],
                        'required' => $options['doc_required'] ?? true,
                        'validation_groups' => $options['validation_groups'],
                    ]
                )
                ->add('lien', UrlType::class, [])
                ->add('entreprise')
                ->add('utilisateur', EntityType::class, [
                    'class' => UserFront::class,
                    'choice_label' => 'getEmailUser',
                    'placeholder' => 'Selectionnez un utilisateur',
                    'attr' => ['class' => 'form-select'],
                    'label' => 'Utilisateur simple',
                    'required' => false,

                ])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'getNomComplet',
                    'placeholder' => 'Selectionnez un quartier',
                    'attr' => ['class' => 'form-select'],
                    'label' => 'Quartier',
                    'required' => true,

                ]);
            $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-default btn-sm', 'data-bs-dismiss' => 'modal']])
                ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-main btn-ajax btn-sm']])
                ->add('rejeter', SubmitType::class, ['label' => 'Rejeter la demande', 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])
                ->add('passer', SubmitType::class, ['label' => 'Valider la demande', 'attr' => ['class' => 'btn btn-success btn-ajax btn-sm']]);
        } else {
            $builder->add('message');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sponsoring::class,
            'doc_required' => true,
            'fichiers' => false,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired('type');
        $resolver->setRequired(['validation_groups']);
    }
}
