<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Prestataire;
use App\Entity\PropositionService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PropositionServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['type'];



        if ($type == "rejeter") {
            $builder->add('messageRejeter', TextareaType::class, []);
        } else {


            $builder
                ->add('libelle')
                ->add('categorie', EntityType::class, [
                    'class' => Categorie::class,
                    'choice_label' => 'libelle',
                    'label' => 'Catégroie',
                    'attr' => ['class' => 'has-select2 form-select'],
                    'placeholder' => 'Choisissez une categorie',
                    'constraints' => new NotBlank(['message' => 'Selectionnez une  categorie']),
                ])
                /* ->add('etat')
            ->add('dateCreation') */
                ->add('prestataire', EntityType::class, [
                    'class' => Prestataire::class,
                    'choice_label' => 'denominationSociale',
                    'label' => 'Prestataire',
                    'attr' => ['class' => 'has-select2 form-select']
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
            'data_class' => PropositionService::class,
            // 'doc_required' => true,
        ]);
        //$resolver->setRequired('doc_options');
        // $resolver->setRequired('doc_required');
        $resolver->setRequired('type');
    }
}
