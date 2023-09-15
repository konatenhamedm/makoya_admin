<?php

namespace App\Controller\Parametre\Prestation;

use App\Entity\PropositionService;
use App\Form\PropositionServiceType;
use App\Repository\PropositionServiceRepository;
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

#[Route('/ads/parametre/prestation/proposition/service')]
class PropositionServiceController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_parametre_prestation_proposition_service_index';
    #[Route('/ads/{etat}', name: 'app_parametre_prestation_proposition_service_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $etat, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()

            ->add('dateCreation', DateTimeColumn::class, ['label' => 'Date création', 'format' => 'd-m-Y'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => PropositionService::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select('e')
                        ->from(PropositionService::class, 'e')
                        ->andWhere('e.etat = :etat')
                        ->setParameter('etat', $etat);
                }
            ])
            ->setName('dt_app_parametre_prestation_proposition_service' . $etat);
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, PropositionService $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_proposition_service_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_proposition_service_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_prestation_proposition_service_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
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


        return $this->render('parametre/prestation/proposition_service/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat
        ]);
    }

    #[Route('/ads/new/add', name: 'app_parametre_prestation_proposition_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropositionServiceRepository $propositionServiceRepository, FormError $formError): Response
    {
        $propositionService = new PropositionService();
        $form = $this->createForm(PropositionServiceType::class, $propositionService, [
            'method' => 'POST',
            'type' => 'allData',
            'action' => $this->generateUrl('app_parametre_prestation_proposition_service_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_workflow_index');


            if ($form->isValid()) {
                $propositionService->setEtat('proposition_initie');
                $propositionServiceRepository->save($propositionService, true);
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

        return $this->renderForm('parametre/prestation/proposition_service/new.html.twig', [
            'proposition_service' => $propositionService,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/show', name: 'app_parametre_prestation_proposition_service_show', methods: ['GET'])]
    public function show(PropositionService $propositionService): Response
    {
        return $this->render('parametre/prestation/proposition_service/show.html.twig', [
            'proposition_service' => $propositionService,
        ]);
    }

    #[Route('/ads/{id}/edit', name: 'app_parametre_prestation_proposition_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropositionService $propositionService, PropositionServiceRepository $propositionServiceRepository, FormError $formError): Response
    {

        $form = $this->createForm(PropositionServiceType::class, $propositionService, [
            'method' => 'POST',
            'type' => 'allData',
            'action' => $this->generateUrl('app_parametre_prestation_proposition_service_edit', [
                'id' =>  $propositionService->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_workflow_index');
            $workflow = $this->workflow->get($propositionService, 'add_proposition_service');

            if ($form->isValid()) {
                if ($form->getClickedButton()->getName() === 'passer') {
                    $workflow->apply($propositionService, 'passer');


                    $propositionServiceRepository->save($propositionService, true);
                } elseif ($form->getClickedButton()->getName() === 'rejeter') {
                    $workflow->apply($propositionService, 'rejeter');

                    $propositionServiceRepository->save($propositionService, true);
                } else {
                    $propositionServiceRepository->save($propositionService, true);
                }

                // $propositionServiceRepository->save($propositionService, true);
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

        return $this->renderForm('parametre/prestation/proposition_service/edit.html.twig', [
            'proposition_service' => $propositionService,
            'form' => $form,
        ]);
    }


    #[Route('/ads/{id}/rejeter', name: 'app_parametre_prestation_proposition_service_rejeter', methods: ['GET', 'POST'])]
    public function Rejeter(Request $request, PropositionService $propositionService, PropositionServiceRepository $propositionServiceRepository, FormError $formError): Response
    {
        //dd();
        $form = $this->createForm(PropositionServiceType::class, $propositionService, [
            'method' => 'POST',
            'type' => 'rejeter',
            'action' => $this->generateUrl('app_parametre_prestation_proposition_service_rejeter', [
                'id' =>  $propositionService->getId()
            ])
        ]);
        //    dd($form->getData());

        /*foreach ($form->getData()->getAvis()-as $el){

            $demande->
        }*/

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_workflow_index');
            $workflow = $this->workflow->get($propositionService, 'add_proposition_service');

            if ($form->isValid()) {
                //dd($workflow->can($demande,'document_verification_refuse'));
                if ($workflow->can($propositionService, 'rejeter')) {
                    $workflow->apply($propositionService, 'rejeter');
                    $this->em->flush();
                }
                $propositionServiceRepository->save($propositionService, true);
                // $demandeRepository->save($demande, true);



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

        return $this->renderForm('workflowdemande/workflow_service_prestataire/rejeter.html.twig', [
            'workflowServicePrestataire' => $propositionService,
            // 'fichiers' => $repository->findOneBySomeFields($demande),
            'form' => $form,
        ]);
    }


    #[Route('/ads/{id}/delete', name: 'app_parametre_prestation_proposition_service_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, PropositionService $propositionService, PropositionServiceRepository $propositionServiceRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_prestation_proposition_service_delete',
                    [
                        'id' => $propositionService->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $propositionServiceRepository->remove($propositionService, true);

            $redirect = $this->generateUrl('app_parametre_prestation_proposition_service_index');

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

        return $this->renderForm('parametre/prestation/proposition_service/delete.html.twig', [
            'proposition_service' => $propositionService,
            'form' => $form,
        ]);
    }
}
