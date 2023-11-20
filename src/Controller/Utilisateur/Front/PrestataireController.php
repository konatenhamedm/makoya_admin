<?php

namespace App\Controller\Utilisateur\Front;

use App\Entity\Prestataire;
use App\Form\PrestataireType;
use App\Repository\PrestataireRepository;
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
use App\Repository\CommuneRepository;
use App\Repository\QuartierRepository;
use App\Repository\RegionRepository;
use App\Repository\ServicePrestataireRepository;
use App\Repository\SousCategorieRepository;

#[Route('/ads/utilisateur/front/prestataire')]
class PrestataireController extends BaseController
{

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Prestataire::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'PR' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    const INDEX_ROOT_NAME = 'app_utilisateur_front_prestataire_index';

    #[Route('/ads/', name: 'app_utilisateur_front_prestataire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('denominationSociale', TextColumn::class, ['label' => 'Dénomination'])
            ->add('email', TextColumn::class, ['label' => 'Email'])
            ->add('contactPrincipal', TextColumn::class, ['label' => 'Contact'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Prestataire::class,
            ])
            ->setName('dt_app_utilisateur_front_prestataire');
        if ($permission != null) {

            $renders = [
                'edit' =>  new ActionRender(function () use ($permission) {
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
                    } else {
                        return true;
                    }
                }),
                'edit_service' =>  new ActionRender(function () use ($permission) {
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
                    } else {
                        return true;
                    }
                }),
                'change_password' =>  new ActionRender(function () use ($permission) {
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
                    } else {
                        return true;
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
                    } else {
                        return true;
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
                    } else {
                        return true;
                    }
                    return true;
                }),
                'show_service' => new ActionRender(function () use ($permission) {
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
                    } else {
                        return true;
                    }
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
                $table->add('reference', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Prestataire $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_utilisateur_front_prestataire_edit', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_utilisateur_front_prestataire_show', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],

                                'change_password' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_utilisateur_front_prestataire_change_password', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-lock', 'attrs' => ['class' => 'btn-success'], 'render' => $renders['change_password']
                                ],
                                'edit_service' => [
                                    'target' => '#exampleModalSizeLg2',
                                    'url' => $this->generateUrl('app_utilisateur_front_prestataire_edit_service', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-plus-square text-light', 'attrs' => ['class' => 'btn-main', 'title' => 'Ajouter des services'], 'render' => $renders['edit_service']
                                ],
                                'show_service' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_prestataire_service_index', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-book', 'attrs' => ['class' => 'btn-primary', 'title' => 'Liste services'], 'render' => $renders['show_service']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_utilisateur_front_prestataire_delete', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
                                ]
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


        return $this->render('utilisateur/front/prestataire/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_front_prestataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PrestataireRepository $prestataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $prestataire = new Prestataire();
        $form = $this->createForm(PrestataireType::class, $prestataire, [
            'method' => 'POST',
            'type' => 'prestataire',
            'password' => 'password',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_prestataire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_prestataire_index');
            //$quartier = $form->get('quartier')->getData();
            $password = $form->get('password')->getData();

            if ($form->isValid()) {
                $prestataire->setPassword($this->hasher->hashPassword($prestataire, $password));
                $prestataire->setReference($this->numero());
                //$prestataire->setQuartier($quartier);
                $prestataireRepository->save($prestataire, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
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

        return $this->renderForm('utilisateur/front/prestataire/new.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{reference}/show', name: 'app_utilisateur_front_prestataire_show', methods: ['GET'])]
    public function show(Prestataire $prestataire): Response
    {
        return $this->render('utilisateur/front/prestataire/show.html.twig', [
            'prestataire' => $prestataire,
        ]);
    }

    #[Route('/{reference}/edit', name: 'app_utilisateur_front_prestataire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prestataire $prestataire, PrestataireRepository $prestataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(PrestataireType::class, $prestataire, [
            'method' => 'POST',
            'type' => 'prestataire',
            'password' => 'nopassword',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_prestataire_edit', [
                'reference' =>  $prestataire->getReference()
            ])
        ]);
        //dd($prestataire);
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_prestataire_index');
            $quartier = $form->get('quartier')->getData();


            if ($form->isValid()) {
                //dd($quartier);
                $prestataire->setQuartier($quartier);
                $prestataireRepository->save($prestataire, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
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

        return $this->renderForm('utilisateur/front/prestataire/edit.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }
    #[Route('/{reference}/change/password', name: 'app_utilisateur_front_prestataire_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, Prestataire $prestataire, PrestataireRepository $prestataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(PrestataireType::class, $prestataire, [
            'method' => 'POST',
            'type' => 'prestataire',
            'password' => 'password',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_prestataire_change_password', [
                'reference' =>  $prestataire->getReference()
            ])
        ]);
        //dd($prestataire);
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_prestataire_index');
            // $quartier = $form->get('quartier')->getData();
            $password = $form->get('password')->getData();

            if ($form->isValid()) {
                //dd($quartier);
                $prestataire->setPassword($this->hasher->hashPassword($prestataire, $password));
                //$prestataire->setQuartier($quartier);
                $prestataireRepository->save($prestataire, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
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

        return $this->renderForm('utilisateur/front/prestataire/password.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{reference}/edit/service', name: 'app_utilisateur_front_prestataire_edit_service', methods: ['GET', 'POST'])]
    public function editService(Request $request, Prestataire $prestataire, PrestataireRepository $prestataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(PrestataireType::class, $prestataire, [
            'method' => 'POST',
            'type' => 'service',
            'password' => 'nopassword',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_prestataire_edit_service', [
                'reference' =>  $prestataire->getReference()
            ])
        ]);
        //dd($prestataire);
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_prestataire_index');


            if ($form->isValid()) {

                $prestataireRepository->save($prestataire, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
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

        return $this->renderForm('utilisateur/front/prestataire/service.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{reference}/delete', name: 'app_utilisateur_front_prestataire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Prestataire $prestataire, PrestataireRepository $prestataireRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_utilisateur_front_prestataire_delete',
                    [
                        'reference' => $prestataire->getReference()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $prestataireRepository->remove($prestataire, true);

            $redirect = $this->generateUrl('app_utilisateur_front_prestataire_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
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

        return $this->renderForm('utilisateur/front/prestataire/delete.html.twig', [
            'prestataire' => $prestataire,
            'form' => $form,
        ]);
    }

    #[Route('/liste/souscategorie', name: 'get_souscategorie', methods: ['GET'])]
    public function getInfoSerie(Request $request, SousCategorieRepository $sousCategorieRepository, ServicePrestataireRepository $servicePrestataireRepository)
    {
        $response = new Response();
        $tabEnsemblesSousCate = array();

        $id = '';
        $id = $request->get('id');
        //dd( $id);
        if ($id) {

            $dataSousCat = $sousCategorieRepository->findBy(array("categorie" => $id));


            //dd($dataSousCat);
            $i = 0;


            foreach ($dataSousCat as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabEnsemblesSousCate[$i]['id'] = $e->getId();
                $tabEnsemblesSousCate[$i]['libelle'] = $e->getLibelle();
                $i++;
            }

            $dataSousCategorie = json_encode($tabEnsemblesSousCate); // formater le résultat de la requête en json


            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataSousCategorie);
        }
        return $response;
    }

    #[Route('/liste/service', name: 'get_service', methods: ['GET'])]
    public function getService(Request $request, SousCategorieRepository $sousCategorieRepository, ServicePrestataireRepository $servicePrestataireRepository)
    {
        $response = new Response();
        $tabEnsemblesService = array();

        $id = '';
        $id = $request->get('id');
        //dd( $id);
        if ($id) {


            $dataService = $servicePrestataireRepository->findBy(array("categorie" => $id));

            //dd($ensembles);

            $i = 0;

            foreach ($dataService as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabEnsemblesService[$i]['id'] = $e->getId();
                $tabEnsemblesService[$i]['libelle'] = $e->getLibelle();
                $i++;
            }

            $dataService = json_encode($tabEnsemblesService); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);



            // dd($response);

        }
        return $response;
    }



    #[Route('/liste/communes', name: 'get_commune', methods: ['GET'])]
    public function getCommune(Request $request, CommuneRepository $communeRepository)
    {
        $response = new Response();
        $tabEnsemblesCommune = array();

        $id = '';
        $id = $request->get('id');
        //dd( $id);
        if ($id) {


            $data = $communeRepository->createQueryBuilder('c')
                ->innerJoin('c.sousPrefecture', 's')
                ->innerJoin('s.departement', 'd')
                ->innerJoin('d.region', 'r')
                ->andWhere('r.id =:region')
                ->setParameter('region', $id)
                ->orderBy('s.id', 'ASC')
                ->getQuery()
                ->getResult();

            /*   $dataQuartier = $this->quartierReprository->createQueryBuilder('q')
            ->innerJoin('q.commune', 'c')
            ->innerJoin('c.sousPrefecture', 's')
            ->innerJoin('s.departement', 'd')
            ->innerJoin('d.region', 'r')
            ->andWhere('r.id =:region')
            ->setParameter('region', $this->regionReprository->findOneBy(array('code' => 'RG1')))
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult(); */

            //$dataService = $servicePrestataireRepository->findBy(array("categorie" => $id));

            //dd($ensembles);

            $i = 0;

            foreach ($data as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabEnsemblesCommune[$i]['id'] = $e->getId();
                $tabEnsemblesCommune[$i]['libelle'] = $e->getNom();
                $i++;
            }

            $dataCommune = json_encode($tabEnsemblesCommune); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataCommune);



            // dd($response);

        }
        return $response;
    }
    #[Route('/getCommuneRegionQuartier', name: 'get_commune_region_quartier', methods: ['GET'])]
    public function getCommuneRegionQuartier(Request $request, QuartierRepository $quartierRepository)
    {
        $response = new Response();
        $tabEnsemblesCommune = array();

        $id = '';
        $id = $request->get('id');
        //dd( $id);
        if ($id) {


            $dataCommuneRegion = $quartierRepository->createQueryBuilder('q')
                ->select('c.id as commune,r.id as region')
                ->innerJoin('q.commune', 'c')
                ->innerJoin('c.sousPrefecture', 's')
                ->innerJoin('s.departement', 'd')
                ->innerJoin('d.region', 'r')
                ->andWhere('q.id =:quartier')
                ->setParameter('quartier', $id)
                ->getQuery()
                ->getSingleResult();

            //dd($dataCommune);



            $data = json_encode($dataCommuneRegion); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);



            // dd($response);

        }
        return $response;
    }
    #[Route('/liste/regions', name: 'get_regions', methods: ['GET'])]
    public function getRegions(Request $request, RegionRepository $regionRepository)
    {
        $response = new Response();
        $tabEnsemblesQuartier = array();



        $dataRegions = $regionRepository->createQueryBuilder('r')
            ->getQuery()
            ->getResult();

        //dd($dataRegions);

        $i = 0;

        foreach ($dataRegions as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
            $tabEnsemblesQuartier[$i]['id'] = $e->getId();
            $tabEnsemblesQuartier[$i]['libelle'] = $e->getNom();
            $i++;
        }

        //dd()



        $data = json_encode($tabEnsemblesQuartier); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);


        return $response;
    }

    #[Route('/liste/quartiers', name: 'get_quartier', methods: ['GET'])]
    public function getQuartier(Request $request, QuartierRepository $quartierRepository)
    {
        $response = new Response();
        $tabEnsemblesQuartier = array();

        $id = '';
        $id = $request->get('id');
        //dd( $id);
        if ($id) {



            $data = $quartierRepository->createQueryBuilder('q')
                ->innerJoin('q.commune', 'c')
                ->innerJoin('c.sousPrefecture', 's')
                ->innerJoin('s.departement', 'd')
                ->innerJoin('d.region', 'r')
                ->andWhere('r.id =:region')
                ->setParameter('region', $id)
                ->orderBy('q.id', 'ASC')
                ->getQuery()
                ->getResult();

            //$dataService = $servicePrestataireRepository->findBy(array("categorie" => $id));

            //dd($ensembles);

            $i = 0;

            foreach ($data as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabEnsemblesQuartier[$i]['id'] = $e->getId();
                $tabEnsemblesQuartier[$i]['libelle'] = $e->getNom();
                $i++;
            }

            $dataQuartier = json_encode($tabEnsemblesQuartier); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataQuartier);



            // dd($response);

        }
        return $response;
    }

    #[Route('/liste/quartier/communes', name: 'get_quartier_commune', methods: ['GET'])]
    public function getQuartierCommune(Request $request, QuartierRepository $quartierRepository)
    {
        $response = new Response();
        $tabEnsemblesQuartier = array();

        $id = '';
        $id = $request->get('id');
        //dd( $id);
        if ($id) {



            $data = $quartierRepository->createQueryBuilder('q')
                ->innerJoin('q.commune', 'c')
                ->andWhere('c.id =:commune')
                ->setParameter('commune', $id)
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult();

            //$dataService = $servicePrestataireRepository->findBy(array("categorie" => $id));

            //dd($ensembles);

            $i = 0;

            foreach ($data as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabEnsemblesQuartier[$i]['id'] = $e->getId();
                $tabEnsemblesQuartier[$i]['libelle'] = $e->getNom();
                $i++;
            }

            $dataQuartier = json_encode($tabEnsemblesQuartier); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataQuartier);



            // dd($response);

        }
        return $response;
    }
}
