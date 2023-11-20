<?php

namespace App\Form;

use App\Entity\Civilite;
use App\Entity\Commune;
use App\Entity\Quartier;
use App\Entity\Region;
use App\Entity\UtilisateurSimple;
use App\Repository\CommuneRepository;
use App\Repository\QuartierRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UtilisateurSimpleType extends AbstractType
{

    private $communeReprository;
    private $quartierReprository;
    private $regionReprository;
    public function __construct(CommuneRepository $communeRepository, QuartierRepository $quartierRepository, RegionRepository $regionRepository)
    {
        $this->quartierReprository = $quartierRepository;
        $this->communeReprository = $communeRepository;
        $this->regionReprository = $regionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $type = $options['type'];
        $password = $options['password'];
        if ($password == "password" && $type != "service") {
            $builder->add(
                'password',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            );

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $departement = $event->getData()->getQuartier();
                //dd($departement);
                if ($event->getData()) {
                    $dataCommunes = $this->communeReprository->createQueryBuilder('c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('s.id', 'ASC')
                        ->getQuery()
                        ->getResult();

                    //dd($dataCommune);

                    $dataQuartier = $this->quartierReprository->createQueryBuilder('q')
                        ->innerJoin('q.commune', 'c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('q.id', 'ASC')
                        ->getQuery()
                        ->getResult();


                    $event->getForm()->add('commune',  EntityType::class, [
                        'class' => Commune::class,
                        'choice_label' => 'nom',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->innerJoin('c.sousPrefecture', 's')
                                ->innerJoin('s.departement', 'd')
                                ->innerJoin('d.region', 'r')
                                ->andWhere('r =:region')
                                ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                                ->orderBy('c.id', 'ASC');
                        },
                        'mapped' => false,
                        'label' => 'Région',
                        'attr' => ['class' => 'has-select2 form-select commune']
                    ]);
                    /* $event->getForm()->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'nom',
                    'choices' => $dataQuartier,
                    'mapped' => false,
                    'disabled' => false,
                    'attr' => ['class' => 'has-select2 quartier'],
                    'placeholder' => 'Selectionnez un quartier',
                    'constraints' => new NotBlank(['message' => 'Selectionnez un quartier']),
                ]); */
                } else {
                    $dataCommunes = $this->communeReprository->createQueryBuilder('c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('s.id', 'ASC')
                        ->getQuery()
                        ->getResult();

                    $dataQuartiers = $this->quartierReprository->createQueryBuilder('q')
                        ->innerJoin('q.commune', 'c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('q.id', 'ASC')
                        ->getQuery()
                        ->getResult();

                    $event->getForm()->add('commune', EntityType::class, [
                        'class' => Commune::class,
                        'choice_label' => 'nom',
                        'choices' => $dataCommunes,
                        'mapped' => false,
                        'disabled' => false,
                        'attr' => ['class' => 'has-select2 commune'],
                        'placeholder' => 'Selectionnez une commune',

                    ]);
                    $event->getForm()->add('quartier', EntityType::class, [
                        'class' => Quartier::class,
                        'choice_label' => 'nom',
                        'choices' => $dataQuartiers,
                        'mapped' => false,
                        'disabled' => false,
                        'attr' => ['class' => 'has-select2 quartier'],
                        'placeholder' => 'Selectionnez un quartier',
                        'constraints' => new NotBlank(['message' => 'Selectionnez un quartier']),
                    ]);
                }
            });

            $builder
                ->add('username', TextType::class, ['label' => 'Pseudo'])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'nom',
                    'label' => 'Quartier',
                    'attr' => ['class' => 'has-select2 form-select quartier']
                ])
                /*    ->add(
                    'password',
                    RepeatedType::class,
                    [
                        'type'            => PasswordType::class,
                        'invalid_message' => 'Les mots de passe doivent être identiques.',
                        'required'        => $options['passwordRequired'],
                        'first_options'   => ['label' => 'Mot de passe'],
                        'second_options'  => ['label' => 'Répétez le mot de passe'],
                    ]
                ) */
                ->add(
                    'photo',
                    FichierType::class,
                    [
                        'label' => 'Fichier',
                        'label' => 'Photo',
                        'doc_options' => $options['doc_options'],
                        'required' => $options['doc_required'] ?? true,
                        'validation_groups' => $options['validation_groups'],
                    ]
                )

                ->add('email')
                ->add('nom')
                ->add('prenoms')
                ->add('contact')
                ->add('genre', EntityType::class, [
                    'placeholder' => '----',
                    'class' => Civilite::class,
                    'choice_label' => 'libelle',
                    'label' => 'Genre',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('longitude')
                ->add('lattitude')
                ->add('commune', EntityType::class, [
                    'class' => Commune::class,
                    'choice_label' => 'nom',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.id', 'ASC');
                    },
                    'mapped' => false,
                    'label' => 'Commune',
                    'attr' => ['class' => 'has-select2 form-select commune']
                ])
                ->add('region', EntityType::class, [
                    'class' => Region::class,
                    'choice_label' => 'nom',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.id', 'ASC');
                    },
                    'mapped' => false,
                    'label' => 'Région',
                    'attr' => ['class' => 'has-select2 form-select region']
                ]);
        } elseif ($password == "nopassword") {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $departement = $event->getData()->getQuartier();
                //dd($departement);
                if ($event->getData()) {
                    $dataCommunes = $this->communeReprository->createQueryBuilder('c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('s.id', 'ASC')
                        ->getQuery()
                        ->getResult();

                    //dd($dataCommune);

                    $dataQuartier = $this->quartierReprository->createQueryBuilder('q')
                        ->innerJoin('q.commune', 'c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('q.id', 'ASC')
                        ->getQuery()
                        ->getResult();


                    $event->getForm()->add('commune',  EntityType::class, [
                        'class' => Commune::class,
                        'choice_label' => 'nom',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->innerJoin('c.sousPrefecture', 's')
                                ->innerJoin('s.departement', 'd')
                                ->innerJoin('d.region', 'r')
                                ->andWhere('r =:region')
                                ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                                ->orderBy('c.id', 'ASC');
                        },
                        'mapped' => false,
                        'label' => 'Région',
                        'attr' => ['class' => 'has-select2 form-select commune']
                    ]);
                    /* $event->getForm()->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'nom',
                    'choices' => $dataQuartier,
                    'mapped' => false,
                    'disabled' => false,
                    'attr' => ['class' => 'has-select2 quartier'],
                    'placeholder' => 'Selectionnez un quartier',
                    'constraints' => new NotBlank(['message' => 'Selectionnez un quartier']),
                ]); */
                } else {
                    $dataCommunes = $this->communeReprository->createQueryBuilder('c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('s.id', 'ASC')
                        ->getQuery()
                        ->getResult();

                    $dataQuartiers = $this->quartierReprository->createQueryBuilder('q')
                        ->innerJoin('q.commune', 'c')
                        ->innerJoin('c.sousPrefecture', 's')
                        ->innerJoin('s.departement', 'd')
                        ->innerJoin('d.region', 'r')
                        ->andWhere('r =:region')
                        ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'REG-ABJ1')))
                        ->orderBy('q.id', 'ASC')
                        ->getQuery()
                        ->getResult();

                    $event->getForm()->add('commune', EntityType::class, [
                        'class' => Commune::class,
                        'choice_label' => 'nom',
                        'choices' => $dataCommunes,
                        'mapped' => false,
                        'disabled' => false,
                        'attr' => ['class' => 'has-select2 commune'],
                        'placeholder' => 'Selectionnez une commune',

                    ]);
                    $event->getForm()->add('quartier', EntityType::class, [
                        'class' => Quartier::class,
                        'choice_label' => 'nom',
                        'choices' => $dataQuartiers,
                        'mapped' => false,
                        'disabled' => false,
                        'attr' => ['class' => 'has-select2 quartier'],
                        'placeholder' => 'Selectionnez un quartier',
                        'constraints' => new NotBlank(['message' => 'Selectionnez un quartier']),
                    ]);
                }
            });

            $builder
                ->add('username', TextType::class, ['label' => 'Pseudo'])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'nom',
                    'label' => 'Quartier',
                    'attr' => ['class' => 'has-select2 form-select quartier']
                ])
                /*    ->add(
                    'password',
                    RepeatedType::class,
                    [
                        'type'            => PasswordType::class,
                        'invalid_message' => 'Les mots de passe doivent être identiques.',
                        'required'        => $options['passwordRequired'],
                        'first_options'   => ['label' => 'Mot de passe'],
                        'second_options'  => ['label' => 'Répétez le mot de passe'],
                    ]
                ) */
                ->add(
                    'photo',
                    FichierType::class,
                    [
                        'label' => 'Fichier',
                        'label' => 'Photo',
                        'doc_options' => $options['doc_options'],
                        'required' => $options['doc_required'] ?? true,
                        'validation_groups' => $options['validation_groups'],
                    ]
                )

                ->add('email')
                ->add('nom')
                ->add('prenoms')
                ->add('contact')
                ->add('genre', EntityType::class, [
                    'placeholder' => '----',
                    'class' => Civilite::class,
                    'choice_label' => 'libelle',
                    'label' => 'Genre',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('longitude')
                ->add('lattitude')
                ->add('commune', EntityType::class, [
                    'class' => Commune::class,
                    'choice_label' => 'nom',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.id', 'ASC');
                    },
                    'mapped' => false,
                    'label' => 'Commune',
                    'attr' => ['class' => 'has-select2 form-select commune']
                ])
                ->add('region', EntityType::class, [
                    'class' => Region::class,
                    'choice_label' => 'nom',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')
                            ->orderBy('m.id', 'ASC');
                    },
                    'mapped' => false,
                    'label' => 'Région',
                    'attr' => ['class' => 'has-select2 form-select region']
                ]);
        } elseif ($password == "changePassword" && $type != "service") {
            $builder->add(
                'password',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UtilisateurSimple::class,
            'passwordRequired' => false,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired('passwordRequired');
        $resolver->setRequired(['type']);
        $resolver->setRequired(['password']);
        $resolver->setRequired(['validation_groups']);
    }
}
