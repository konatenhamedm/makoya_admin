<?php

namespace App\Controller\Resaux;

use App\Entity\Signaler;
use App\Form\SignalerType;
use App\Repository\SignalerRepository;
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

#[Route('/ads/resaux/signaler')]
class SignalerController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_resaux_signaler_index';

    #[Route('/ads/', name: 'app_resaux_signaler_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('dateCreation', DateTimeColumn::class, ['label' => 'Identifiant', 'format' => 'Y-d-m'])
            ->add('utilisateur', TextColumn::class, ['label' => 'Utilisateur', 'field' => 'u.nom'])
            ->add('prestataire', TextColumn::class, ['label' => 'Prestataire', 'field' => 'p.denominationSociale'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Signaler::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('s, p,u')
                        ->from(Signaler::class, 's')
                        ->join('s.utilisateur', 'u')
                        ->join('s.prestataire', 'p')
                        ->andWhere('s.etat = :etat')
                        ->setParameter('etat', 1);
                }
            ])
            ->setName('dt_app_resaux_signaler');
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
                    } elseif ($permission == 'CRUD') {
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
                    } elseif ($permission == 'CRUD') {
                        return true;
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
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Signaler $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                /*   'edit' => [
                                    'url' => $this->generateUrl('app_resaux_signaler_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ], */
                                'show' => [
                                    'url' => $this->generateUrl('app_resaux_signaler_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                /*  'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_resaux_signaler_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('resaux/signaler/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/ads/new', name: 'app_resaux_signaler_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SignalerRepository $signalerRepository, FormError $formError): Response
    {
        $signaler = new Signaler();
        $form = $this->createForm(SignalerType::class, $signaler, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_resaux_signaler_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_resaux_signaler_index');


            if ($form->isValid()) {

                $signalerRepository->save($signaler, true);
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

        return $this->renderForm('resaux/signaler/new.html.twig', [
            'signaler' => $signaler,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/show', name: 'app_resaux_signaler_show', methods: ['GET'])]
    public function show(Signaler $signaler): Response
    {
        return $this->render('resaux/signaler/show.html.twig', [
            'signaler' => $signaler,
        ]);
    }

    #[Route('/ads/{id}/edit', name: 'app_resaux_signaler_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Signaler $signaler, SignalerRepository $signalerRepository, FormError $formError): Response
    {

        $form = $this->createForm(SignalerType::class, $signaler, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_resaux_signaler_edit', [
                'id' => $signaler->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_resaux_signaler_index');


            if ($form->isValid()) {

                $signalerRepository->save($signaler, true);
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

        return $this->renderForm('resaux/signaler/edit.html.twig', [
            'signaler' => $signaler,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/delete', name: 'app_resaux_signaler_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Signaler $signaler, SignalerRepository $signalerRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_resaux_signaler_delete',
                    [
                        'id' => $signaler->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $signalerRepository->remove($signaler, true);

            $redirect = $this->generateUrl('app_resaux_signaler_index');

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

        return $this->renderForm('resaux/signaler/delete.html.twig', [
            'signaler' => $signaler,
            'form' => $form,
        ]);
    }
}
