<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\SousPrefecture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SousPrefectureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('nom')
            ->add('departement', EntityType::class, [
                'placeholder'=>'----',
                'class' => Departement::class,
                'choice_label' => 'nom',
                'label' => 'Département',
                'attr' => ['class' => 'has-select2 form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SousPrefecture::class,
        ]);
    }
}
