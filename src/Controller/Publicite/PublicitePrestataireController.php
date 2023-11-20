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
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/publicite/publicite/prestataire')]
class PublicitePrestataireController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_publicite_publicite_prestataire_index';
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
        $publicitePrestataire = new PublicitePrestataire();
        $form = $this->createForm(PublicitePrestataireType::class, $publicitePrestataire, [
            'method' => 'POST',
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

        $form = $this->createForm(PublicitePrestataireType::class, $publicitePrestataire, [
            'method' => 'POST',
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
