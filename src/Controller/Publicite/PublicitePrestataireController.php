<?php

namespace App\Controller\Publicite;

use App\Entity\PublicitePrestataire;
use App\Form\PublicitePrestataireType;
use App\Repository\PublicitePrestataireRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\Publicite;
use App\Entity\PubliciteDemande;
use App\Entity\UserFront;
use App\Repository\PubliciteDemandeRepository;
use App\Repository\PubliciteRepository;
use App\Service\Menu;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[Route('/ads/publicite/publicite/prestataire')]
class PublicitePrestataireController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_publicite_publicite_prestataire_index';


    #[Route('/imprime/all', name: 'app_suivi_print_all', methods: ['GET'])]
    public function imprimer(PubliciteDemandeRepository $publicitePrestataireRepository): Response
    {
        //dd($publicitePrestataireRepository->findAll());
        $imgFiligrame = "uploads/" . 'logo' . "/" . 'logo.png';
        return $this->renderPdf("publicite/publicite_prestataire/imprime_all.html.twig", [
            'data' => $publicitePrestataireRepository->findAll(),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'L',
            'protected' => true,

            'format' => 'A5',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ]
        ], true, "", $imgFiligrame);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }

    #[Route('/imprime/autre/all', name: 'app_suivi_autre_print_all', methods: ['GET'])]
    public function imprimerSuiviAutre(PubliciteRepository $publiciteRepository): Response
    {
        //dd($publiciteRepository->findAll());
        $imgFiligrame = "uploads/" . 'logo' . "/" . 'logo.png';
        return $this->renderPdf("publicite/publicite_prestataire/imprime_autre_all.html.twig", [
            'data' => $publiciteRepository->findPubliciteBy(),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'L',
            'protected' => true,

            'format' => 'A5',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ]
        ], true, "", $imgFiligrame);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }

    #[Route('/suivi/all', name: 'app_publicite_publicite_prestataire_suivi_autre_index',  methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexSuiviAutre(Request $request, DataTableFactory $dataTableFactory, Menu $menu, PubliciteRepository $publiciteRepository): Response
    {


        $datetime = new \DateTime("now");
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), "app_publicite_publicite_prestataire_suivi_autre_index");

        /*  $niveau = $request->query->get('niveau');*/
        $etat = $request->query->get('etat');
        $type = $request->query->get('type');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        //$mode = $request->query->get('mode');


        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_publicite_publicite_prestataire_suivi_autre_index', compact('dateDebut', 'dateFin', 'type', 'etat'))
        ])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un type',
                    'label' => 'Type publicité',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'CAT' => 'Categorie',
                        'ENC' => 'Encart',
                        'RGP' => 'Région'
                    ]),
                ]
            )
            ->add(
                'etat',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un etat',
                    'label' => 'Etat',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'EN_COURS' => 'En cours',
                        'PRESQUE' => 'Presque',
                        'TERMINER' => 'Terminer'
                    ]),
                ]
            )

            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ]);


        $table = $dataTableFactory->create()

            ->add('type', TextColumn::class, ['label' => 'Type utilisateur', 'render' => function ($value, Publicite $context) use ($datetime) {

                if (str_contains($context->getCode(), "CAT") == true) {
                    $label = 'Type catégorie';
                    $color = 'danger';
                } elseif (str_contains($context->getCode(), "RGP") == true) {
                    $label = 'Type région';
                    $color = 'warning';
                } elseif (str_contains($context->getCode(), "ENC") == true) {
                    $label = 'Type encart';
                    $color = 'success';
                } else {
                    $label = 'Autre';
                    $color = 'primary';
                }

                return $label;
            }])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('entite', TextColumn::class, ['field' => 'l.id', 'label' => 'Categorie', 'render' => function ($value, Publicite $context) {

                if (str_contains($context->getCode(), "CAT") == true) {
                    $color = 'danger';
                    $label = $this->menu->getPubTypeLibelle('CAT', $context->getCode());
                } elseif (str_contains($context->getCode(), "RGP") == true) {
                    $color = 'warning';
                    $label = $this->menu->getPubTypeLibelle('RGP', $context->getCode());
                } elseif (str_contains($context->getCode(), "ENC") == true) {
                    $color = 'success';
                    $label = $this->menu->getPubTypeLibelle('ENC', $context->getCode());
                }


                return $label;
            }])
            ->add('etatV', TextColumn::class, ['className' => 'w-1px', 'field' => 'l.id', 'label' => 'Etat', 'render' => function ($value, Publicite $context) use ($datetime) {
                $diff_in_days = floor((strtotime($context->getDateFin()->format("Y-m-d")) - strtotime($datetime->format("Y-m-d"))) / (60 * 60 * 24));

                //dd($diff_in_days);
                if (intval($diff_in_days) == 0  || intval($diff_in_days) < 0) {
                    $label = 'Terminer';
                    $color = 'danger';
                } elseif (intval($diff_in_days) < 8 && intval($diff_in_days)  > 0) {
                    $label = 'Presque';
                    $color = 'warning';
                } else {
                    $label = 'En cours';
                    $color = 'success';
                }

                return sprintf('<span class="badge badge-%s">%s</span>', $color, $label);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => PublicitePrestataire::class,
                'query' => function (QueryBuilder $qb) use ($dateDebut, $dateFin, $etat, $type, $datetime) {
                    $qb->select('e')
                        ->from(Publicite::class, 'e')
                        ->andWhere('e.code LIKE :cat or e.code LIKE :rgp or e.code LIKE :enc')
                        ->setParameter('cat', '%CAT%')
                        ->setParameter('rgp', '%RGP%')
                        ->setParameter('enc', '%ENC%');
                    /* ->leftJoin('e.utilisateur', 'u'); */

                    if ($dateDebut || $dateFin || $etat || $type) {
                        /* if ($etat) {
                            $qb->andWhere('e.etat = :etat')
                                ->setParameter('etat', $etat);
                        }
 */

                        if ($type) {
                            if ($type == 'CAT') {
                                $qb->andWhere('e.code LIKE :cat')
                                    ->setParameter('cat', '%CAT%');
                            } elseif ($type == 'RGP') {
                                $qb->andWhere('e.code LIKE :rgp')
                                    ->setParameter('rgp', '%RGP%');
                            } elseif ($type == 'ENC') {
                                $qb->andWhere('e.code LIKE :enc')
                                    ->setParameter('enc', '%ENC%');
                            }
                        }

                        if ($dateDebut) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin) {
                            //date_format($date, 'Y-m-d H:i:s')2024-02-02 10:54:11;
                            $truc = explode('-', str_replace("/", "-", $dateFin));

                            //  dd($truc);
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {
                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0] . ' ' . '00:00:00';

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }


                        if ($etat) {

                            if ($etat == "TERMINER") {
                                $qb->andWhere('e.dateFin = :current_date or DATE_DIFF(e.dateFin, :current_date) < 0')
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } elseif ($etat == "PRESQUE") {
                                $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) < 8 and  DATE_DIFF(e.dateFin, :current_date) > 0")
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } else {
                                $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) >= 8 ")
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            }
                        }
                    }
                }
            ])
            ->setName('dt_app_publicite_publicite_prestataire_suivi_autre_'  . $etat . '_' . $type);
        if ($permission != null) {
            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

            ];

            $gridId =  $etat . '_' . $type;
            // dd($gridId);

            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('code', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Publicite $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                /* 'edit' => [
                                'url' => $this->generateUrl('app_publicite_publicite_prestataire_edit', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                            ], */
                                'show' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_categorie_show', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                /* 'delete' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_publicite_publicite_prestataire_delete', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                            ] */
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('publicite/publicite_prestataire/index_suivi_autre.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm()->createView(),
            'grid_id' => $gridId,
            'permition' => $permission,
        ]);
    }



    #[Route('/suivi', name: 'app_publicite_publicite_prestataire_suivi_index',  methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexSuivi(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $datetime = new \DateTime("now");
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), "app_publicite_publicite_prestataire_suivi_index");

        /*  $niveau = $request->query->get('niveau');*/
        $etat = $request->query->get('etat');
        $user = $request->query->get('user');
        $type = $request->query->get('type');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        //$mode = $request->query->get('mode');


        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_publicite_publicite_prestataire_suivi_index', compact('dateDebut', 'dateFin', 'user', 'type', 'etat'))
        ])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un type',
                    'label' => 'Type utilisateur',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'Prestataire' => 'Prestataire',
                        'Utilisateur simple' => 'Utilisateur simple'
                    ]),
                ]
            )
            ->add(
                'etat',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un etat',
                    'label' => 'Etat',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'EN_COURS' => 'En cours',
                        'PRESQUE' => 'Presque',
                        'TERMINER' => 'Terminer'
                    ]),
                ]
            )
            ->add('user', EntityType::class, [
                'class' => UserFront::class,
                'choice_label' => 'username',
                'label' => 'Utilisateur',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ]);


        $table = $dataTableFactory->create()
            ->add('type', TextColumn::class, ['label' => 'Type utilisateur'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('utilisateur', TextColumn::class, ['label' => 'Utilisateur', 'render' => function ($value, PubliciteDemande $context) {
                // dd($context->getUtilisateur());
                if (str_contains($context->getType(), "Prestataire") == true) {
                    $label = $context->getUtilisateur()->getDenominationSociale();
                    $color = 'danger';
                } else {
                    $label = $context->getUtilisateur()->getNomComplet();
                    $color = 'warning';
                }

                return $label;
            }])
            ->add('email', TextColumn::class, ['label' => 'Email', 'field' => 'u.email'])
            ->add('etatV', TextColumn::class, ['className' => 'w-1px', 'field' => 'l.id', 'label' => 'Etat', 'render' => function ($value, PubliciteDemande $context) use ($datetime) {
                $diff_in_days = floor((strtotime($context->getDateFin()->format("Y-m-d")) - strtotime($datetime->format("Y-m-d"))) / (60 * 60 * 24));

                //dd($diff_in_days);
                if (intval($diff_in_days) == 0  || intval($diff_in_days) < 0) {
                    $label = 'Terminer';
                    $color = 'danger';
                } elseif (intval($diff_in_days) < 8 && intval($diff_in_days)  > 0) {
                    $label = 'Presque';
                    $color = 'warning';
                } else {
                    $label = 'En cours';
                    $color = 'success';
                }

                return sprintf('<span class="badge badge-%s">%s</span>', $color, $label);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => PubliciteDemande::class,
                'query' => function (QueryBuilder $qb) use ($dateDebut, $dateFin, $user, $etat, $type, $datetime) {
                    $qb->select('e')
                        ->from(PubliciteDemande::class, 'e')
                        ->leftJoin('e.utilisateur', 'u');

                    if ($dateDebut || $dateFin ||  $user || $etat || $type) {
                        /* if ($etat) {
                            $qb->andWhere('e.etat = :etat')
                                ->setParameter('etat', $etat);
                        }
 */
                        if ($user) {
                            $qb->andWhere('u.id = :user')
                                ->setParameter('user', $user);
                        }
                        if ($type) {
                            $qb->andWhere('e.type = :type')
                                ->setParameter('type', $type);
                        }

                        if ($dateDebut) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin) {
                            //date_format($date, 'Y-m-d H:i:s')2024-02-02 10:54:11;
                            $truc = explode('-', str_replace("/", "-", $dateFin));

                            //  dd($truc);
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {
                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0] . ' ' . '00:00:00';

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }


                        if ($etat) {

                            if ($etat == "TERMINER") {
                                $qb->andWhere('e.dateFin = :current_date or DATE_DIFF(e.dateFin, :current_date) < 0')
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } elseif ($etat == "PRESQUE") {
                                $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) < 8 and  DATE_DIFF(e.dateFin, :current_date) > 0")
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } else {
                                $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) >= 8 ")
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            }
                        }
                    }
                }
            ])
            ->setName('dt_app_publicite_publicite_prestataire_suivi_' . $user . '_' . $etat . '_' . $type);
        if ($permission != null) {
            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

            ];

            $gridId = $user . '_' . $etat . '_' . $type;
            // dd($gridId);

            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('code', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, PubliciteDemande $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                /* 'edit' => [
                                'url' => $this->generateUrl('app_publicite_publicite_prestataire_edit', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                            ], */
                                'show' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_show', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                /* 'delete' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_publicite_publicite_prestataire_delete', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                            ] */
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('publicite/publicite_prestataire/index_suivi.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm()->createView(),
            'grid_id' => $gridId,
            'permition' => $permission,
        ]);
    }





    #[Route('/{etat}', name: 'app_publicite_publicite_prestataire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $etat, DataTableFactory $dataTableFactory): Response
    {

        $datetime = new \DateTime("now");
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('type', TextColumn::class, ['label' => 'Type utilisateur'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('utilisateur', TextColumn::class, ['label' => 'Utilisateur', 'field' => 'u.username'])
            ->add('email', TextColumn::class, ['label' => 'Email', 'field' => 'u.email'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => PublicitePrestataire::class,
                'query' => function (QueryBuilder $qb) use ($etat, $datetime) {
                    $qb->select('e')
                        ->from(PublicitePrestataire::class, 'e')
                        ->leftJoin('e.utilisateur', 'u');


                    if ($etat == "terminer") {
                        $qb->andWhere('e.dateFin = :current_date or DATE_DIFF(e.dateFin, :current_date) < 0')
                            ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                    } elseif ($etat == "en_cours_peremption") {
                        $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) < 8 and  DATE_DIFF(e.dateFin, :current_date) > 0")
                            ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                    } else {
                        $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) >= 8 ")
                            ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                    }
                }
            ])
            ->setName('dt_app_publicite_publicite_prestataire' . $etat);
        if ($permission != null) {

            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

            ];


            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('code', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, PublicitePrestataire $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                /* 'edit' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_prestataire_edit', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ], */
                                'show' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_prestataire_show', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                /* 'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_publicite_publicite_prestataire_delete', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                                ] */
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('publicite/publicite_prestataire/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat
        ]);
    }

    #[Route('/pub/new', name: 'app_publicite_publicite_prestataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PublicitePrestataireRepository $publicitePrestataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $publicitePrestataire = new PublicitePrestataire();
        $form = $this->createForm(PublicitePrestataireType::class, $publicitePrestataire, [
            'method' => 'POST',
            'type' => 'image',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_publicite_publicite_prestataire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_publicite_index');


            if ($form->isValid()) {

                $publicitePrestataireRepository->save($publicitePrestataire, true);
                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('publicite/publicite_prestataire/new.html.twig', [
            'publicite_prestataire' => $publicitePrestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/show', name: 'app_publicite_publicite_prestataire_show', methods: ['GET'])]
    public function show(PublicitePrestataire $publicitePrestataire): Response
    {
        return $this->render('publicite/publicite_prestataire/show.html.twig', [
            'publicite_prestataire' => $publicitePrestataire,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_publicite_publicite_prestataire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PublicitePrestataire $publicitePrestataire, PublicitePrestataireRepository $publicitePrestataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(PublicitePrestataireType::class, $publicitePrestataire, [
            'method' => 'POST',
            'type' => 'autre',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_publicite_publicite_prestataire_edit', [
                'code' => $publicitePrestataire->getCode()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_publicite_index');


            if ($form->isValid()) {

                $publicitePrestataireRepository->save($publicitePrestataire, true);
                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('publicite/publicite_prestataire/edit.html.twig', [
            'publicite_prestataire' => $publicitePrestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/delete', name: 'app_publicite_publicite_prestataire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, PublicitePrestataire $publicitePrestataire, PublicitePrestataireRepository $publicitePrestataireRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_publicite_publicite_prestataire_delete',
                    [
                        'code' => $publicitePrestataire->getCode()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $publicitePrestataireRepository->remove($publicitePrestataire, true);

            $redirect = $this->generateUrl('app_config_publicite_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut' => 1,
                'message' => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('publicite/publicite_prestataire/delete.html.twig', [
            'publicite_prestataire' => $publicitePrestataire,
            'form' => $form,
        ]);
    }
}
