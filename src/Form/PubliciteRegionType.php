<?php

namespace App\Form;

use App\Entity\Jours;
use App\Entity\PubliciteRegion;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class PubliciteRegionType extends AbstractType
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
            ])
            ->add('dateFin',  DateType::class, [
                'label' => 'Date fin',
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto skip-init'],
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'widget' => 'single_text',
            ])
            ->add('jours', EntityType::class, [
                'label'        => 'Jours',
                'choice_label' => 'libelle',
                /*  'choice_attr' => function (InfoSerie $info) {
            return ['data-value' => $info->getid()];
        }, */
                'multiple'     => true,
                'expanded'     => false,
                'placeholder' => 'Choisir des jours',
                'attr' => ['class' => 'has-select2 jours'],
                'class'        => Jours::class,
            ])
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'nom',
                'placeholder' => 'Selectionnez une region',
                'attr' => ['class' => 'categorie form-select'],
                'label' => false,
                'required' => true,
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champs prestataire")
                )
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PubliciteRegion::class,
        ]);
    }
}
