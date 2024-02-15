<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\PrestataireService;
use App\Entity\ServicePrestataire;
use App\Entity\SousCategorie;
use App\Repository\ServicePrestataireRepository;
use App\Repository\SousCategorieRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrestataireServiceType extends AbstractType
{
    private $repoSousCategorie;
    private $repoService;

    public function __construct(SousCategorieRepository $repoSousCategorie, ServicePrestataireRepository $repoService)
    {
        $this->repoSousCategorie = $repoSousCategorie;
        $this->repoService = $repoService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*  $builder->get('categorie')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            dd($data);
        }); */

        $builder

            /* ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {


                //$departement = $event->getData()['departement'] ?? null;

                $dataSousCategorie = $this->repoSousCategorie->createQueryBuilder('s')
                    ->innerJoin('s.categorie', 'c')
                    ->andWhere('c.code =:categorie')
                    ->setParameter('categorie', "ff")
                    ->orderBy('s.id', 'ASC')
                    ->getQuery()
                    ->getResult();
                // dd($event->getData());

                $dataService = $this->repoService->createQueryBuilder('s')
                    ->innerJoin('s.categorie', 'c')
                    ->andWhere('c.code =:categorie')
                    ->setParameter('categorie',  "fff")
                    ->orderBy('s.id', 'ASC')
                    ->getQuery()
                    ->getResult();

                $event->getForm()->add('service', EntityType::class, [
                    'class' => ServicePrestataire::class,
                    'choice_label' => 'libelle',
                    'choices' => $dataService,
                    'disabled' => false,
                    'attr' => ['class' => 'has-select2 form-select service'],
                    'placeholder' => 'Selectionnez un service',
                    //'constraints' => new NotBlank(['message' => 'Selectionnez un service']),
                ]);
                $event->getForm()->add('sousCategorie', EntityType::class, [
                    'class' => SousCategorie::class,
                    'choice_label' => 'libelle',
                    'choices' => $dataSousCategorie,
                    'disabled' => false,
                    'attr' => ['class' => 'has-select2 form-select sousCategorie'],
                    'placeholder' => 'Selectionnez une sous categorie',
                ]);
            }) */
            ->add('etat', CheckboxType::class, [
                'label' => 'dependre logo principal', 'required' => false,
                'attr' => [
                    'style' => 'margin-top:29px'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégorie',
                'required' => false,
                'constraints' => new NotBlank(['message' => 'Selectionnez une  categorie']),
                'placeholder' => 'Selectionnez une  categorie',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.id', 'ASC');
                },
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select categorie']
            ])
            ->add('sousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'choice_label' => 'libelle',
                'label' => 'Sous catégorie',
                'required' => false,
                // 'placeholder' => '----',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.id', 'ASC');
                },
                'attr' => ['class' => 'has-select2 form-select sousCategorie']
            ])
            ->add('service', EntityType::class, [
                'class' => ServicePrestataire::class,
                'choice_label' => 'libelle',
                'label' => 'Service',
                'required' => false,
                // 'placeholder' => '----',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.id', 'ASC');
                },
                'attr' => ['class' => 'has-select2 form-select service'],
                'constraints' => new NotBlank(['message' => 'Selectionnez un service']),
            ])
            ->add(
                'image',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'image',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrestataireService::class,
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
