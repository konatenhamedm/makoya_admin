<?php

namespace App\Form;

use App\Entity\Civilite;
use App\Entity\Prestataire;
use App\Entity\Quartier;
use DoctrineExtensions\Query\Mysql\Quarter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestataireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {  $type = $options['type'];
       //dd($type);
        if($type == "service"){
            $builder->add('prestataireServices', CollectionType::class, [
                'entry_type' => PrestataireServiceType::class,
                'entry_options' => [
                    'label' => false,
                    'doc_options' => $options['doc_options'],
                    'doc_required' => $options['doc_required']
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ]);
        }
            if($type !="service"){
                $builder->add('username', TextType::class, ['label' => 'Pseudo'])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'nom',
                    'label' => 'Quartier',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('password', RepeatedType::class, 
                    [
                        'type'            => PasswordType::class,
                        'invalid_message' => 'Les mots de passe doivent être identiques.',
                        'required'        => $options['passwordRequired'],
                        'first_options'   => ['label' => 'Mot de passe'],
                        'second_options'  => ['label' => 'Répétez le mot de passe'],
                    ]
                )
                ->add('email')
                ->add('denominationSociale')
                ->add('logo', FichierType::class,
                ['label' => 'Fichier',
                    'label' => 'Logo',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true])
                ->add('contactPrincipal')
                ->add('longitude')
                ->add('lattitude');
            }

          
           

     
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestataire::class,
            'passwordRequired' => false,
            'doc_required' => true,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired('passwordRequired');
        $resolver->setRequired(['type']);
       
    }

 
}
