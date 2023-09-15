<?php

namespace App\Controller\Parametre\Prestation;

use App\Entity\ServicePrestataire;
use App\Form\ServicePrestataireType;
use App\Repository\ServicePrestataireRepository;
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
use App\Entity\Service;

#[Route('/ads/parametre/prestation/service/prestataire')]
class ServicePrestataireController extends BaseController
{
    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(ServicePrestataire::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'SERV' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    const INDEX_ROOT_NAME = 'app_parametre_prestation_service_prestataire_index';
    #[Route('/ads/', name: 'app_parametre_prestation_service_prestataire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ServicePrestataire::class,
            ])
            ->setName('dt_app_parametre_prestation_service_prestataire');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, ServicePrestataire $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_service_prestataire_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_parametre_prestation_service_prestataire_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_prestation_service_prestataire_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
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


        return $this->render('parametre/prestation/service_prestataire/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/ads/new', name: 'app_parametre_prestation_service_prestataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServicePrestataireRepository $servicePrestataireRepository, FormError $formError): Response
    {
        $servicePrestataire = new ServicePrestataire();
        $form = $this->createForm(ServicePrestataireType::class, $servicePrestataire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_prestation_service_prestataire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_prestation_service_prestataire_index');


            if ($form->isValid()) {
                $servicePrestataire->setCode($this->numero());
                $servicePrestataireRepository->save($servicePrestataire, true);
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

        return $this->renderForm('parametre/prestation/service_prestataire/new.html.twig', [
            'service_prestataire' => $servicePrestataire,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/show', name: 'app_parametre_prestation_service_prestataire_show', methods: ['GET'])]
    public function show(ServicePrestataire $servicePrestataire): Response
    {
        return $this->render('parametre/prestation/service_prestataire/show.html.twig', [
            'service_prestataire' => $servicePrestataire,
        ]);
    }

    #[Route('/ads/{id}/edit', name: 'app_parametre_prestation_service_prestataire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ServicePrestataire $servicePrestataire, ServicePrestataireRepository $servicePrestataireRepository, FormError $formError): Response
    {

        $form = $this->createForm(ServicePrestataireType::class, $servicePrestataire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_prestation_service_prestataire_edit', [
                'id' =>  $servicePrestataire->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_prestation_service_prestataire_index');


            if ($form->isValid()) {

                $servicePrestataireRepository->save($servicePrestataire, true);
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

        return $this->renderForm('parametre/prestation/service_prestataire/edit.html.twig', [
            'service_prestataire' => $servicePrestataire,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/delete', name: 'app_parametre_prestation_service_prestataire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, ServicePrestataire $servicePrestataire, ServicePrestataireRepository $servicePrestataireRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_prestation_service_prestataire_delete',
                    [
                        'id' => $servicePrestataire->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $servicePrestataireRepository->remove($servicePrestataire, true);

            $redirect = $this->generateUrl('app_parametre_prestation_service_prestataire_index');

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

        return $this->renderForm('parametre/prestation/service_prestataire/delete.html.twig', [
            'service_prestataire' => $servicePrestataire,
            'form' => $form,
        ]);
    }
}
