<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd(str_contains($options['validation_groups'], "FileRequired"));
        $builder
            /*  ->add('code') */
            ->add('libelle')
            ->add(
                'imageLaUne',
                FichierType::class,
                [
                    'label' => 'Icon catégorie',
                /*    'label' => false,*/
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                    /* 'constraints' => [

                        in_array('FileRequired', $options['validation_groups']) ? new NotBlank(null, "Veuillez renseigner le fichiesr") : "",

                    ], */
                ]
            )
        ->add(
        'image',
        FichierType::class,
        [
            'label' => 'Image détails',
            /*    'label' => false,*/
            'doc_options' => $options['doc_options'],
            'required' => $options['doc_required'] ?? true,
            'validation_groups' => $options['validation_groups'],
            /* 'constraints' => [

                in_array('FileRequired', $options['validation_groups']) ? new NotBlank(null, "Veuillez renseigner le fichiesr") : "",

            ], */
        ]
    );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
