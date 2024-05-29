<?php

namespace App\Controller\Publicite;

use App\Entity\PubliciteDemande;
use App\Form\PubliciteDemandeType;
use App\Repository\PubliciteDemandeRepository;
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
use App\Entity\Notification;
use App\Entity\UtilisateurSimple;
use App\Form\PubliciteDemandeSimpleType;
use App\Service\NotificationService;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/publicite/publicite/demande')]
class PubliciteDemandeController extends BaseController
{


    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(PubliciteDemande::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'DMP' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    const INDEX_ROOT_NAME = 'app_publicite_publicite_demande_index';
    #[Route('/{etat}', name: 'app_publicite_publicite_demande_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $etat, DataTableFactory $dataTableFactory): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            // ->add('utilisateur', TextColumn::class, ['label' => 'Prestatire', 'field' => 'p.denominationSociale'])

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
            ->createAdapter(ORMAdapter::class, [
                'entity' => PubliciteDemande::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select('e, p')
                        ->from(PubliciteDemande::class, 'e')
                        ->join('e.utilisateur', 'p')
                        ->andWhere('e.etat = :etat')
                        ->setParameter('etat', $etat);
                }
            ])
            ->setName('dt_app_publicite_publicite_demande' . $etat);
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, PubliciteDemande $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_edit', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_show', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_delete', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('publicite/publicite_demande/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat
        ]);
    }

    #[Route('/pubs/new/user/simple', name: 'app_publicite_publicite_demande_utilisateur_simple_new', methods: ['GET', 'POST'])]
    public function newUserSimple(Request $request, PubliciteDemandeRepository $publiciteDemandeRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $publiciteDemande = new PubliciteDemande();
        $form = $this->createForm(PubliciteDemandeSimpleType::class, $publiciteDemande, [
            'method' => 'POST',
            'type' => 'image',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_publicite_index');


            if ($form->isValid()) {
                $publiciteDemande->setCode($this->numero());
                $publiciteDemande->setEtat('demande_initie');
                $publiciteDemande->setType('Utilisateur simple');
                $publiciteDemandeRepository->save($publiciteDemande, true);
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

        return $this->renderForm('publicite/publicite_demande_utilisateur_simple/new.html.twig', [
            'publicite_demande_utilisateur_simple' => $publiciteDemande,
            'form' => $form,
        ]);
    }

    #[Route('/pubs/new', name: 'app_publicite_publicite_demande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PubliciteDemandeRepository $publiciteDemandeRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $publiciteDemande = new PubliciteDemande();
        $form = $this->createForm(PubliciteDemandeType::class, $publiciteDemande, [
            'method' => 'POST',
            'type' => 'image',
            'nature' => "Prestataire",
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_publicite_publicite_demande_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_publicite_index');


            if ($form->isValid()) {
                $publiciteDemande->setCode($this->numero());
                $publiciteDemande->setEtat('demande_initie');
                $publiciteDemande->setType('Prestataire');
                $publiciteDemandeRepository->save($publiciteDemande, true);
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

        return $this->renderForm('publicite/publicite_demande/new.html.twig', [
            'publicite_demande' => $publiciteDemande,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/show', name: 'app_publicite_publicite_demande_show', methods: ['GET'])]
    public function show(PubliciteDemande $publiciteDemande): Response
    {
        return $this->render('publicite/publicite_demande/show.html.twig', [
            'publicite_demande' => $publiciteDemande,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_publicite_publicite_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PubliciteDemande $publiciteDemande, PubliciteDemandeRepository $publiciteDemandeRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(PubliciteDemandeType::class, $publiciteDemande, [
            'method' => 'POST',
            'type' => 'edit',
            'nature' => $publiciteDemande->getType(),
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_publicite_publicite_demande_edit', [
                'code' => $publiciteDemande->getCode()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_publicite_index');

            $workflow = $this->workflow->get($publiciteDemande, 'add_demande_publicite');

            if ($form->isValid()) {
                if ($form->getClickedButton()->getName() === 'passer') {
                    $workflow->apply($publiciteDemande, 'passer');


                    $publiciteDemandeRepository->save($publiciteDemande, true);
                } elseif ($form->getClickedButton()->getName() === 'rejeter') {
                    $workflow->apply($publiciteDemande, 'rejeter');

                    $publiciteDemandeRepository->save($publiciteDemande, true);
                } else {
                    $publiciteDemandeRepository->save($publiciteDemande, true);
                }

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

        return $this->renderForm('publicite/publicite_demande/edit.html.twig', [
            'publicite_demande' => $publiciteDemande,
            'form' => $form,
        ]);
    }


    #[Route('/{code}/rejeter', name: 'app_publicite_publicite_demande_rejeter', methods: ['GET', 'POST'])]
    public function Rejeter(Request $request, PubliciteDemande $publiciteDemande, PubliciteDemandeRepository $publiciteDemandeRepository, FormError $formError): Response
    {
        //dd();
        $form = $this->createForm(PubliciteDemandeType::class, $publiciteDemande, [
            'method' => 'POST',
            'type' => 'rejeter',
            'action' => $this->generateUrl('app_publicite_publicite_demande_rejeter', [
                'code' =>  $publiciteDemande->getCode()
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
            $workflow = $this->workflow->get($publiciteDemande, 'add_demande_publicite');

            if ($form->isValid()) {
                //dd($workflow->can($demande,'document_verification_refuse'));
                if ($workflow->can($publiciteDemande, 'rejeter')) {
                    $workflow->apply($publiciteDemande, 'rejeter');
                    $this->em->flush();
                }


                $publiciteDemandeRepository->save($publiciteDemande, true);
                // $demandeRepository->save($demande, true);



                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
                $this->redirectToRoute("app_config_workflow_index");
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
            'workflowServicePrestataire' => $publiciteDemande,
            // 'fichiers' => $repository->findOneBySomeFields($demande),
            'form' => $form,
        ]);
    }


    #[Route('/{code}/delete', name: 'app_publicite_publicite_demande_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, PubliciteDemande $publiciteDemande, PubliciteDemandeRepository $publiciteDemandeRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_publicite_publicite_demande_delete',
                    [
                        'code' => $publiciteDemande->getCode()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $publiciteDemandeRepository->remove($publiciteDemande, true);

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

        return $this->renderForm('publicite/publicite_demande/delete.html.twig', [
            'publicite_demande' => $publiciteDemande,
            'form' => $form,
        ]);
    }
}
