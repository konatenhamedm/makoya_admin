<?php

namespace App\Form;

use App\Entity\Civilite;
use App\Entity\Quartier;
use App\Entity\UtilisateurSimple;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurSimpleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, ['label' => 'Pseudo'])
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
        ->add('photo', FichierType::class,
        ['label' => 'Fichier',
            'label' => 'Photo',
            'doc_options' => $options['doc_options'],
            'required' => $options['doc_required'] ?? true])
       
            ->add('email')
            ->add('nom')
            ->add('prenoms')
            ->add('contact')
            ->add('genre', EntityType::class, [
                'placeholder'=>'----',
                'class' => Civilite::class,
                'choice_label' => 'libelle',
                'label' => 'Genre',
                'attr' => ['class' => 'has-select2 form-select']
            ])
            ->add('longitude')
            ->add('lattitude')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UtilisateurSimple::class,
            'passwordRequired' => false,
            'doc_required' => true,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired('passwordRequired');
    }
}
