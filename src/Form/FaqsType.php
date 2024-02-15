<?php

namespace App\Form;

use App\Entity\Faqs;
use App\Entity\TypeFaqs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaqsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('libelle')
            ->add('type', EntityType::class, [
                'class' => TypeFaqs::class,
                'choice_label' => 'libelle',
                'label' => 'Type faq',
                'attr' => ['class' => 'has-select2 form-select']
            ])
            ->add('reponse');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Faqs::class,
        ]);
    }
}
