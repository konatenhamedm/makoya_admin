<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*  ->add('code') */
            ->add('libelle')
            ->add(
                'imageLaUne',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Image',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            'doc_required' => true,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
