<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Prestataire;
use App\Entity\ServicePrestataire;
use App\Entity\SousCategorie;
use App\Entity\WorkflowServicePrestataire;
use App\Repository\ServicePrestataireRepository;
use App\Repository\SousCategorieRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class WorkflowServicePrestataireType extends AbstractType
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

        $type = $options['type'];



        if ($type == "rejeter") {
            $builder->add('messageRejeter', TextareaType::class, []);
        } else {
            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    //$departement = $event->getData()['departement'] ?? null;
                    if ($event->getData()) {
                    } else {
                        $dataSousCategorie = $this->repoSousCategorie->createQueryBuilder('s')
                            ->innerJoin('s.categorie', 'c')
                            ->andWhere('c.code =:categorie')
                            ->setParameter('categorie', "Cat01")
                            ->orderBy('s.id', 'ASC')
                            ->getQuery()
                            ->getResult();

                        $dataService = $this->repoService->createQueryBuilder('s')
                            ->innerJoin('s.categorie', 'c')
                            ->andWhere('c.code =:categorie')
                            ->setParameter('categorie', "Cat01")
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

                        ]);
                        $event->getForm()->add('sousCategorie', EntityType::class, [
                            'class' => SousCategorie::class,
                            'choice_label' => 'libelle',
                            'choices' => $dataSousCategorie,
                            'disabled' => false,
                            'attr' => ['class' => 'has-select2 form-select sousCategorie'],
                            'placeholder' => 'Selectionnez une sous categorie',

                        ]);
                    }
                })
                ->add(
                    'image',
                    FichierType::class,
                    [
                        'label' => 'Fichier',
                        'label' => 'image',
                        'doc_options' => $options['doc_options'],
                        'required' => $options['doc_required'] ?? true
                    ]
                )

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
                    'attr' => ['class' => 'has-select2 form-select categorie'],

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
                    'attr' => ['class' => 'has-select2 form-select sousCategorie'],
                    'placeholder' => 'Selectionnez une sous catégorie',
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
                    'placeholder' => 'Selectionnez un service',
                    // 'constraints'=>new NotBlank(['message'=>'Selectionnez un service']),
                ])
                ->add('prestataire', EntityType::class, [
                    'class' => Prestataire::class,
                    'choice_label' => 'denominationSociale',
                    'label' => 'Prestataire',
                    'required' => false,
                    'placeholder' => '----',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.id', 'ASC');
                    },
                    'attr' => ['class' => 'has-select2 form-select ']
                ]);
        }

        $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-default btn-sm', 'data-bs-dismiss' => 'modal']])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-main btn-ajax btn-sm']])
            ->add('rejeter', SubmitType::class, ['label' => 'Rejeter la demande', 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])
            ->add('passer', SubmitType::class, ['label' => 'Valider la demande', 'attr' => ['class' => 'btn btn-success btn-ajax btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkflowServicePrestataire::class,
            'doc_required' => true,
            'fichiers' => false,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired('type');
    }
}
