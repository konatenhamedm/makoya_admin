<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\PrestataireService;
use App\Entity\ServicePrestataire;
use App\Entity\SousCategorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestataireServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelle',
                'label' => 'Catégorie',
                'required' => false,
               // 'placeholder' => '----',
                 'query_builder' => function (EntityRepository $er) {
                     return $er->createQueryBuilder('m')
                         ->orderBy('m.id', 'ASC')
                         ;
                 },
                'attr' => ['class' => 'has-select2']
            ])
            ->add('sousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'choice_label' => 'libelle',
                'label' => 'Sous catégorie',
                'required' => false,
               // 'placeholder' => '----',
                 'query_builder' => function (EntityRepository $er) {
                     return $er->createQueryBuilder('m')
                         ->orderBy('m.id', 'ASC')
                         ;
                 },
                'attr' => ['class' => 'has-select2']
            ])
            ->add('service', EntityType::class, [
                'class' => ServicePrestataire::class,
                'choice_label' => 'libelle',
                'label' => 'Service',
                'required' => false,
               // 'placeholder' => '----',
                 'query_builder' => function (EntityRepository $er) {
                     return $er->createQueryBuilder('m')
                         ->orderBy('m.id', 'ASC')
                         ;
                 },
                'attr' => ['class' => 'has-select2']
            ])
            ->add('image', FichierType::class,
            ['label' => 'Fichier',
                'label' => 'image',
                'doc_options' => $options['doc_options'],
                'required' => $options['doc_required'] ?? true
                ]
                )
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrestataireService::class,
            'doc_required' => true,
            'fichiers'=>false,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
