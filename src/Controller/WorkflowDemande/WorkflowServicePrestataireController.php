<?php

namespace App\Controller\WorkflowDemande;

use App\Entity\WorkflowServicePrestataire;
use App\Form\WorkflowServicePrestataireType;
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
use App\Repository\WorkflowServicePrestataireRepository;
use Doctrine\ORM\QueryBuilder;

#[Route('/workflowdemande/workflow/service/prestataire')]
class WorkflowServicePrestataireController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_workflowdemande_workflow_service_prestataire_index';

    #[Route('/{etat}', name: 'app_workflowdemande_workflow_service_prestataire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $etat, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('categorie', TextColumn::class, ['field' => 'categorie.libelle', 'label' => 'Categorie'])
            ->add('service', TextColumn::class, ['field' => 'service.libelle', 'label' => 'Service'])
            ->add('prestataire', TextColumn::class, ['field' => 'prestataire.denominationSociale', 'label' => 'Prestataire'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => WorkflowServicePrestataire::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select('e, categorie, service,prestataire')
                        ->from(WorkflowServicePrestataire::class, 'e')
                        ->join('e.categorie', 'categorie')
                        ->join('e.service', 'service')
                        ->join('e.prestataire', 'prestataire')
                        ->andWhere('e.etat = :etat')
                        ->setParameter('etat', $etat);
                }
            ])
            ->setName('dt_app_workflowdemande_workflow_service_prestataire' . $etat);
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, WorkflowServicePrestataire $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
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


        return $this->render('workflowdemande/workflow_service_prestataire/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat,
        ]);
    }

    #[Route('/{id}/show', name: 'app_workflowdemande_workflow_service_prestataire_show', methods: ['GET'])]
    public function show(WorkflowServicePrestataire $workflowServicePrestataire): Response
    {
        return $this->render('workflowdemande/workflow_service_prestataire/show.html.twig', [
            'workflow_service_prestataire' => $workflowServicePrestataire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workflowdemande_workflow_service_prestataire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkflowServicePrestataire $workflowServicePrestataire, WorkflowServicePrestataireRepository $workflowServicePrestataireRepository, FormError $formError): Response
    {

        $form = $this->createForm(WorkflowServicePrestataireType::class, $workflowServicePrestataire, [
            'method' => 'POST',
            'type' => 'allData',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'action' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_edit', [
                'id' =>  $workflowServicePrestataire->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_workflow_index');
            $workflow = $this->workflow->get($workflowServicePrestataire, 'add_prestation_service');

            if ($form->isValid()) {
                if ($form->getClickedButton()->getName() === 'passer') {
                    $workflow->apply($workflowServicePrestataire, 'passer');


                    $workflowServicePrestataireRepository->save($workflowServicePrestataire, true);
                } elseif ($form->getClickedButton()->getName() === 'rejeter') {
                    $workflow->apply($workflowServicePrestataire, 'rejeter');

                    $workflowServicePrestataireRepository->save($workflowServicePrestataire, true);
                } else {
                    $workflowServicePrestataireRepository->save($workflowServicePrestataire, true);
                }

                //$workflowServicePrestataireRepository->save($workflowServicePrestataire, true);
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

        return $this->renderForm('workflowdemande/workflow_service_prestataire/edit.html.twig', [
            'workflow_service_prestataire' => $workflowServicePrestataire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/rejeter', name: 'app_workflowdemande_workflow_service_prestataire_rejeter', methods: ['GET', 'POST'])]
    public function Rejeter(Request $request, WorkflowServicePrestataire $workflowServicePrestataire, WorkflowServicePrestataireRepository $workflowServicePrestataireRepository, FormError $formError): Response
    {
        //dd();
        $form = $this->createForm(WorkflowServicePrestataireType::class, $workflowServicePrestataire, [
            'method' => 'POST',
            'type' => 'rejeter',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'action' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_rejeter', [
                'id' =>  $workflowServicePrestataire->getId()
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
            $workflow = $this->workflow->get($workflowServicePrestataire, 'add_prestation_service');

            if ($form->isValid()) {
                //dd($workflow->can($demande,'document_verification_refuse'));
                if ($workflow->can($workflowServicePrestataire, 'rejeter')) {
                    $workflow->apply($workflowServicePrestataire, 'rejeter');
                    $this->em->flush();
                }
                $workflowServicePrestataireRepository->save($workflowServicePrestataire, true);
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
            'workflowServicePrestataire' => $workflowServicePrestataire,
            // 'fichiers' => $repository->findOneBySomeFields($demande),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_workflowdemande_workflow_service_prestataire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, WorkflowServicePrestataire $workflowServicePrestataire): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_workflowdemande_workflow_service_prestataire_delete',
                    [
                        'id' => $workflowServicePrestataire->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            //$workflowServicePrestataireRepository->remove($workflowServicePrestataire, true);

            $redirect = $this->generateUrl('app_config_workflow_index');

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

        return $this->renderForm('workflowdemande/workflow_service_prestataire/delete.html.twig', [
            'workflow_service_prestataire' => $workflowServicePrestataire,
            'form' => $form,
        ]);
    }
}
