<?php

namespace App\Controller\Parametre\Prestation;

use App\Entity\PrestataireService;
use App\Form\PrestataireService1Type;
use App\Repository\PrestataireServiceRepository;
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
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/parametre/prestation/prestataire/service')]
class PrestataireServiceController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_parametre_prestation_prestataire_service_index';
    #[Route('/ads/{reference}', name: 'app_parametre_prestation_prestataire_service_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $reference, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('dateCreation', DateTimeColumn::class, ['format' => 'Y-d-m', 'label' => 'Date  création'])
            ->add('categorie', TextColumn::class, ['field' => 'categorie.libelle', 'label' => 'Categorie'])
            ->add('service', TextColumn::class, ['field' => 'service.libelle', 'label' => 'Service'])
            ->add('countVisite', TextColumn::class, ['field' => 'e.countVisite', 'label' => 'Nombre visite'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => PrestataireService::class,
                'query' => function (QueryBuilder $qb) use ($reference) {
                    $qb->select('e, categorie, service,prestataire')
                        ->from(PrestataireService::class, 'e')
                        ->join('e.categorie', 'categorie')
                        ->join('e.service', 'service')
                        ->join('e.prestataire', 'prestataire')
                        ->andWhere('prestataire.reference = :prestataire')
                        ->setParameter('prestataire', $reference);
                }
            ])
            ->setName('dt_app_parametre_prestation_prestataire_service' . $reference);
        if ($permission != null) {

            $renders = [
                /* 'edit' =>  new ActionRender(function () use ($permission) {
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
                }),*/
               /*  'show' => new ActionRender(function () use ($permission) {
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
                }), */
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, PrestataireService $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg22',

                            'actions' => [
                               /*  'show' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_prestataire_service_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ], */
                                /*  'edit' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_prestataire_service_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                               
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_prestation_prestataire_service_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
                                ] */
                            ]

                        ];
                        return $this->renderView('_includes/default_actions_prestataire.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('workflowdemande/workflow_service_prestataire/details_prestataire.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'prestataire' => $reference
        ]);
    }

    #[Route('/ads/new', name: 'app_parametre_prestation_prestataire_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PrestataireServiceRepository $prestataireServiceRepository, FormError $formError): Response
    {
        $prestataireService = new PrestataireService();
        $form = $this->createForm(PrestataireService1Type::class, $prestataireService, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_prestation_prestataire_service_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_prestation_prestataire_service_index');


            if ($form->isValid()) {

                $prestataireServiceRepository->save($prestataireService, true);
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

        return $this->renderForm('parametre/prestation/prestataire_service/new.html.twig', [
            'prestataire_service' => $prestataireService,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/show', name: 'app_parametre_prestation_prestataire_service_show', methods: ['GET'])]
    public function show(PrestataireService $prestataireService): Response
    {
        return $this->render('parametre/prestation/prestataire_service/show.html.twig', [
            'prestataire_service' => $prestataireService,
        ]);
    }

    #[Route('/ads/{id}/edit', name: 'app_parametre_prestation_prestataire_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PrestataireService $prestataireService, PrestataireServiceRepository $prestataireServiceRepository, FormError $formError): Response
    {

        $form = $this->createForm(PrestataireService1Type::class, $prestataireService, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_prestation_prestataire_service_edit', [
                'id' =>  $prestataireService->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_prestation_prestataire_service_index');


            if ($form->isValid()) {

                $prestataireServiceRepository->save($prestataireService, true);
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

        return $this->renderForm('parametre/prestation/prestataire_service/edit.html.twig', [
            'prestataire_service' => $prestataireService,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/delete', name: 'app_parametre_prestation_prestataire_service_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, PrestataireService $prestataireService, PrestataireServiceRepository $prestataireServiceRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_prestation_prestataire_service_delete',
                    [
                        'id' => $prestataireService->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $prestataireServiceRepository->remove($prestataireService, true);

            $redirect = $this->generateUrl('app_parametre_prestation_prestataire_service_index');

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

        return $this->renderForm('parametre/prestation/prestataire_service/delete.html.twig', [
            'prestataire_service' => $prestataireService,
            'form' => $form,
        ]);
    }
}
