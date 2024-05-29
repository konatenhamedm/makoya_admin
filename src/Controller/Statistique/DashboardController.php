<?php

namespace App\Controller\Statistique;

use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Controller\Parametre\Prestation\PrestataireServiceController;
use App\Entity\Categorie;
use App\Entity\Commune;
use App\Entity\Entreprise;
use App\Entity\PrestataireService;
use App\Repository\CategorieRepository;
use App\Repository\NombreClickRepository;
use App\Repository\PrestataireServiceRepository;
use App\Repository\ServicePrestataireRepository;
use App\Repository\SousCategorieRepository;
use App\Service\ActionRender;
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/ads/statistque/dashboard')]
class DashboardController extends BaseController
{
    use FileTrait;
    #[Route('/ads/', name: 'app_president_dashboard_index')]
    public function index(): Response
    {

        $modules = [
            [
                'label' => 'Evolution des catégories',
                'id' => 'chart_one',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_categorie')
            ],
            [
                'label' => 'Evolution des sous catégories',
                'id' => 'chart_two',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_sous_categorie')
            ],
            [
                'label' => 'Evolution des service',
                'id' => 'chart_tree',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_service')
            ],
            [
                'label' => 'Effectif par catégorie et par localité',
                'id' => 'chart_four',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_effectif_localite_categorie')
            ],
            [
                'label' => 'Liste des localités par catégorie',
                'id' => 'chart_py_age',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_liste_localite_by_categoire_index')
            ],
            [
                'label' => 'Classement des entreprises',
                'id' => 'chart_py_anc',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_classement_entreprise_categorie')
            ],
            [
                'label' => 'Statistique fournisseur',
                'id' => 'chart_maitrise',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_fournisseur_effectif_index')
            ],

            [
                'label' => 'Taux couverture catégories par localité',
                'id' => 'chart_compraison',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_taux_couverture_categorie')
            ],
            [
                'label' => 'Avis entreprises',
                'id' => 'chart_classement',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_statistique_note_entreprise_categorie')
            ],
            //

        ];


        return $this->render('statistique_administrative/dashboard.html.twig', [
            'modules' => $modules,
            'titre' => "Dashboard",
        ]);
    }



    #[Route('/action', name: 'app_statistique_action')]
    public function indexAction(): Response
    {

        return $this->render('statistique_administrative/pages/index.html.twig', []);
    }
    #[Route('/classement/entreprise/par/localite/categorie', name: 'app_statistique_classement_entreprise_categorie')]
    public function indexClassementEntreprieParLocaliteCategorie(Request $request, CategorieRepository $categorieRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_categorie'))
            ->setMethod('POST');
        $formBuilder->add('localite', EntityType::class, [
            'choice_label' => 'nom',
            'label' => 'Selectionner une localité',
            'attr' => ['class' => 'has-select2'],
            'choice_attr' => function (Commune $commune) {
                return ['data-value' => $commune->getNom()];
            },
            'class' => Commune::class,
            'required' => false
        ])->add('categorie', EntityType::class, [
            'choice_label' => 'libelle',
            'label' => 'Selectionner une catégorie',
            'attr' => ['class' => 'has-select2'],
            'choice_attr' => function (Categorie $categorie) {
                return ['data-value' => $categorie->getLibelle()];
            },
            'class' => Categorie::class,
            'required' => false
        ]);


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/classement_entreprise.html.twig', [

            'form' => $form->createView(),
            'data' => $categorieRepository->findAll(),
        ]);
    }
    #[Route('/categorie', name: 'app_statistique_categorie')]
    public function indexCategorie(Request $request, CategorieRepository $categorieRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_categorie'))
            ->setMethod('POST');
        $formBuilder->add('dateDebut', DateType::class, [
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


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/categorie.html.twig', [

            'form' => $form->createView(),
            'data' => $categorieRepository->findAll(),
        ]);
    }
    #[Route('/Sous/categorie', name: 'app_statistique_sous_categorie')]
    public function indexSousCategorie(Request $request, SousCategorieRepository $sousCategorieRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_sous_categorie'))
            ->setMethod('POST');
        $formBuilder->add('dateDebut', DateType::class, [
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


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/sous_categorie.html.twig', [

            'form' => $form->createView(),
            'data' => $sousCategorieRepository->findAll(),
        ]);
    }
    #[Route('/effectif/localite/categorie', name: 'app_statistique_effectif_localite_categorie')]
    public function indexEffectifByLocaliteAndCategorie(Request $request, ServicePrestataireRepository $servicePrestataireRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_effectif_localite_categorie'))
            ->setMethod('POST');
        $formBuilder->add('dateDebut', DateType::class, [
            'widget' => 'single_text',
            'label'   => 'Date début',
            'format'  => 'dd/MM/yyyy',
            'required' => false,
            'html5' => false,
            'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
        ])->add('localite', EntityType::class, [
            'choice_label' => 'nom',
            'label' => 'Selectionner une localité',
            'attr' => ['class' => 'has-select2'],
            'choice_attr' => function (Commune $commune) {
                return ['data-value' => $commune->getNom()];
            },
            'class' => Commune::class,
            'required' => false
        ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ]);


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/effectif_localite_categorie.html.twig', [
            'form' => $form->createView(),
            'data' => $servicePrestataireRepository->findAll(),
        ]);
    }

    #[Route('/services', name: 'app_statistique_service')]
    public function indexServices(Request $request, ServicePrestataireRepository $servicePrestataireRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_service'))
            ->setMethod('POST');
        $formBuilder->add('dateDebut', DateType::class, [
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


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/service.html.twig', [
            'form' => $form->createView(),
            'data' => $servicePrestataireRepository->findAll(),
        ]);
    }


    #[Route('/', name: 'app_statistique_liste_localite_by_categoire_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexListeLocaliteByCategoire(Request $request, DataTableFactory $dataTableFactory, NombreClickRepository $nombreClickRepository, PrestataireServiceRepository $prestataireServiceRepository): Response
    {




        /*  $niveau = $request->query->get('niveau');*/
        $categorie = $request->query->get('categorie');
        //$mode = $request->query->get('mode');


        $builder = $this->createFormBuilder(null, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_statistique_liste_localite_by_categoire_index', compact('categorie'))
        ])

            ->add('categorie', EntityType::class, [
                'choice_label' => 'libelle',
                'label' => 'Selectionner une catégorie',
                'attr' => ['class' => 'has-select2'],
                'choice_attr' => function (Categorie $categorie) {
                    return ['data-value' => $categorie->getLibelle()];
                },
                'class' => Categorie::class,
                'required' => false
            ]);




        $table = $dataTableFactory->create()


            /* ->add('code', TextColumn::class, ['label' => 'Code']) */
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('nombreLocalite', TextColumn::class, ['className' => 'w-100px', 'field' => 'l.id', 'label' => 'Nombre de localité', 'render' => function ($value, Categorie $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombre($context->getId(), 'localite');
            }])
            ->add('nombreFournisseur', TextColumn::class, ['className' => 'w-100px', 'field' => 'l.id', 'label' => 'Nombre de prestataire', 'render' => function ($value, Categorie $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombre($context->getId(), 'prestataire');
            }])
            ->add('nombreService', TextColumn::class, ['className' => 'w-100px', 'field' => 'l.id', 'label' => 'Nombre de service', 'render' => function ($value, Categorie $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombre($context->getId(), 'service');
            }])
            ->add('countVisites', TextColumn::class, ['label' => 'Nombre visite', 'render' => function ($value, Categorie $context) use ($nombreClickRepository) {
                return $nombreClickRepository->getNombreVue($context->getId()) ? $nombreClickRepository->getNombreVue($context->getId()) : 0;
            }])
            /*  ->add('dddd', TextColumn::class, ['label' => 'Nombre visite', 'render' => function ($value, Categorie $context) use ($nombreClickRepository) {
                return $context->getNombre();
            }]) */


            ->createAdapter(ORMAdapter::class, [
                'entity' => Categorie::class,
                'query' => function (QueryBuilder $qb) use ($categorie) {
                    $qb->select('e')
                        ->from(Categorie::class, 'e')/* 
                        ->andWhere('e.nombre = :nombre')
                        ->setParameter('nombre', 5) */
                        /* ->innerJoin('e.prestataire', 'p')
                        ->innerJoin('p.quartier', 'q')
                        ->innerJoin('q.commune', 'co')
                        ->innerJoin('e.categorie', 'c') */;
                    /* 
                        ->setParameter('categorie', $categorie) */
                    /*   ->andWhere('e.code LIKE :cat or e.code LIKE :rgp or e.code LIKE :enc')
                        ->setParameter('rgp', '%RGP%')
                        ->setParameter('enc', '%ENC%'); */
                    /* ->leftJoin('e.utilisateur', 'u'); */

                    if ($categorie) {

                        if ($categorie) {

                            $qb->andWhere("e.id = :categorie")
                                ->setParameter('categorie', $categorie);
                        }
                    }
                }
            ])
            ->setName('dt_app_statistique_liste_localite_by_categoire_' . $categorie);


        $renders = [

            'show' => new ActionRender(function () {
                return true;
            }),

        ];

        $gridId =  $categorie;
        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Categorie $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'show' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_localite_categoire',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //, 'render' => new ActionRender(fn() => $source || $etat != 'cree')
                            ],


                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('statistique_administrative/general/liste_localite_categorie.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm()->createView(),
            'grid_id' => $gridId,

        ]);
    }

    #[Route('/imprime/localite/categorie/{id}', name: 'app_localite_categoire', methods: ['GET'])]
    public function imprimer($id, PrestataireServiceRepository $prestataireServiceRepository, CategorieRepository $categorieRepository): Response
    {

        $imgFiligrame = "uploads/" . 'logo' . "/" . 'logo.png';
        return $this->renderPdf("statistique_administrative/imprime/imprime.html.twig", [
            'data' => $prestataireServiceRepository->getAllLocalite($id),
            'data_info' => $categorieRepository->find($id)
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
    #[Route('/imprime/info/fourniseur/{id}', name: 'app_info_fournisseur', methods: ['GET'])]
    public function imprimerFournisseur($id, PrestataireServiceRepository $prestataireServiceRepository, CategorieRepository $categorieRepository, Commune $commune): Response
    {


        //dd($prestataireServiceRepository->getAllUtilisateur($id));
        $imgFiligrame = "uploads/" . 'logo' . "/" . 'logo.png';
        return $this->renderPdf("statistique_administrative/imprime/imprime_fournisseur.html.twig", [
            'data' => [
                'categorie' => $prestataireServiceRepository->getAllCategorie($id),
                'fournisseur' => $prestataireServiceRepository->getAllFournisseur($id),
                'utilisateur' => $prestataireServiceRepository->getAllUtilisateur($id),
            ],
            'data_info' => $commune
        ], [
            'orientation' => 'P',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ]
        ], true, "", $imgFiligrame);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    #[Route('/ads/fournisseurs', name: 'app_statistique_fournisseur_effectif_index', methods: ['GET', 'POST'])]
    public function indexFournisseurs(Request $request, DataTableFactory $dataTableFactory, NombreClickRepository $nombreClickRepository, PrestataireServiceRepository $prestataireServiceRepository): Response
    {

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('nom', TextColumn::class, ['label' => 'Nom'])
            ->add('nombreFournisseur', TextColumn::class, ['className' => 'w-100px', 'label' => 'Nombre de prestataire', 'render' => function ($value, Commune $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombreFournisseur($context->getId())[0]['_total'];
            }])
            ->add('nombreUtilisateur', TextColumn::class, ['className' => 'w-100px', 'label' => 'Nombre utilisateur', 'render' => function ($value, Commune $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombreUtilisateur($context->getId())[0]['_total'];
            }])
            ->add('nombreCategorie', TextColumn::class, ['className' => 'w-100px', 'label' => 'Nombre catégorie', 'render' => function ($value, Commune $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombreCategorie($context->getId())[0]['_total'];
            }])
            /*  ->add('countVisites', TextColumn::class, ['label' => 'Nombre visite', 'render' => function ($value, Categorie $context) use ($nombreClickRepository) {
                return $nombreClickRepository->getNombreVue($context->getId()) ? $nombreClickRepository->getNombreVue($context->getId()) : 0;
            }])
            ->add('nombreService', TextColumn::class, ['className' => 'w-100px', 'field' => 'l.id', 'label' => 'Nombre de service', 'render' => function ($value, Categorie $context) use ($prestataireServiceRepository) {

                return  $prestataireServiceRepository->getNombre($context->getId(), 'service');
            }])*/
            ->createAdapter(ORMAdapter::class, [
                'entity' => Commune::class,
            ])
            ->setName('dt_app_statistique_fournisseur_effectif_index');


        $renders = [


            'show' => new ActionRender(function () {
                return true;
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
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Commune $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'show' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_info_fournisseur',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-primary btn-stack'],
                                'render' => $renders['show']
                            ],
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('statistique_administrative/general/fournisseur.html.twig', [
            'datatable' => $table,
        ]);
    }


    #[Route('/taux/couverture/categorie', name: 'app_statistique_taux_couverture_categorie')]
    public function indexTauxCouvertureCategorie(Request $request, ServicePrestataireRepository $servicePrestataireRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_taux_couverture_categorie'))
            ->setMethod('POST');
        $formBuilder->add('dateDebut', DateType::class, [
            'widget' => 'single_text',
            'label'   => 'Date début',
            'format'  => 'dd/MM/yyyy',
            'required' => false,
            'html5' => false,
            'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
        ])->add('localite', EntityType::class, [
            'choice_label' => 'nom',
            'label' => 'Selectionner une localité',
            'attr' => ['class' => 'has-select2'],
            'choice_attr' => function (Commune $commune) {
                return ['data-value' => $commune->getNom()];
            },
            'class' => Commune::class,
            'required' => false
        ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ]);


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/taux_couverture_categorie.html.twig', [
            'form' => $form->createView(),
            'data' => $servicePrestataireRepository->findAll(),
        ]);
    }

    #[Route('/note/entreprise/par/localite/categorie', name: 'app_statistique_note_entreprise_categorie')]
    public function indexNoteEntreprieParLocaliteCategorie(Request $request, CategorieRepository $categorieRepository): Response
    {

        //  dd($prestataireServiceRepository->getCategorie());
        $formBuilder = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_statistique_categorie'))
            ->setMethod('POST');
        $formBuilder->add('localite', EntityType::class, [
            'choice_label' => 'nom',
            'label' => 'Selectionner une localité',
            'attr' => ['class' => 'has-select2'],
            'choice_attr' => function (Commune $commune) {
                return ['data-value' => $commune->getNom()];
            },
            'class' => Commune::class,
            'required' => false
        ])->add('categorie', EntityType::class, [
            'choice_label' => 'libelle',
            'label' => 'Selectionner une catégorie',
            'attr' => ['class' => 'has-select2'],
            'choice_attr' => function (Categorie $categorie) {
                return ['data-value' => $categorie->getLibelle()];
            },
            'class' => Categorie::class,
            'required' => false
        ]);


        $form = $formBuilder->getForm();

        return $this->render('statistique_administrative/general/avis_entreprise.html.twig', [

            'form' => $form->createView(),
            'data' => $categorieRepository->findAll(),
        ]);
    }
}
