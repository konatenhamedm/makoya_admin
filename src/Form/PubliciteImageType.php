<?php

namespace App\Form;

use App\Entity\PubliciteImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PubliciteImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'image',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Logo',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PubliciteImage::class,
            'doc_required' => true,
            'fichiers' => false,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
