<?php

namespace App\Controller\Publicite;

use App\Entity\PubliciteDemandeUtilisateurSimple;
use App\Form\PubliciteDemandeUtilisateurSimpleType;
use App\Repository\PubliciteDemandeUtilisateurSimpleRepository;
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

#[Route('/ads/publicite/publicite/demande/utilisateur/simple')]
class PubliciteDemandeUtilisateurSimpleController extends BaseController
{


    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(PubliciteDemandeUtilisateurSimple::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'DMUS' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    const INDEX_ROOT_NAME = 'app_publicite_publicite_demande_utilisateur_simple_index';
    #[Route('/{etat}', name: 'app_publicite_publicite_demande_utilisateur_simple_index', methods: ['GET', 'POST'])]
    public function index(Request $request, string $etat, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('utilisateur', TextColumn::class, ['label' => 'Utilisateur', 'field' => 'u.nom'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => PubliciteDemandeUtilisateurSimple::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select('e, u')
                        ->from(PubliciteDemandeUtilisateurSimple::class, 'e')
                        ->join('e.utilisateur', 'u')
                        ->andWhere('e.etat = :etat')
                        ->setParameter('etat', $etat);
                }
            ])
            ->setName('dt_app_publicite_publicite_demande_utilisateur_simple' . $etat);
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
                $table->add('code', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, PubliciteDemandeUtilisateurSimple $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_edit', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_show', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_delete', ['code' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('publicite/publicite_demande_utilisateur_simple/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat
        ]);
    }

    #[Route('/pubs/new', name: 'app_publicite_publicite_demande_utilisateur_simple_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PubliciteDemandeUtilisateurSimpleRepository $publiciteDemandeUtilisateurSimpleRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $publiciteDemandeUtilisateurSimple = new PubliciteDemandeUtilisateurSimple();
        $form = $this->createForm(PubliciteDemandeUtilisateurSimpleType::class, $publiciteDemandeUtilisateurSimple, [
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
                $publiciteDemandeUtilisateurSimple->setCode($this->numero());
                $publiciteDemandeUtilisateurSimple->setEtat('demande_initie');
                $publiciteDemandeUtilisateurSimpleRepository->save($publiciteDemandeUtilisateurSimple, true);
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
            'publicite_demande_utilisateur_simple' => $publiciteDemandeUtilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/show', name: 'app_publicite_publicite_demande_utilisateur_simple_show', methods: ['GET'])]
    public function show(PubliciteDemandeUtilisateurSimple $publiciteDemandeUtilisateurSimple): Response
    {
        return $this->render('publicite/publicite_demande_utilisateur_simple/show.html.twig', [
            'publicite_demande_utilisateur_simple' => $publiciteDemandeUtilisateurSimple,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_publicite_publicite_demande_utilisateur_simple_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PubliciteDemandeUtilisateurSimple $publiciteDemandeUtilisateurSimple, PubliciteDemandeUtilisateurSimpleRepository $publiciteDemandeUtilisateurSimpleRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];

        $form = $this->createForm(PubliciteDemandeUtilisateurSimpleType::class, $publiciteDemandeUtilisateurSimple, [
            'method' => 'POST',
            'type' => 'autre',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_edit', [
                'code' => $publiciteDemandeUtilisateurSimple->getCode()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_publicite_index');
            $workflow = $this->workflow->get($publiciteDemandeUtilisateurSimple, 'add_demande_publicite_utilisateur_simple');

            if ($form->isValid()) {
                if ($form->getClickedButton()->getName() === 'passer') {
                    $workflow->apply($publiciteDemandeUtilisateurSimple, 'passer');


                    $publiciteDemandeUtilisateurSimpleRepository->save($publiciteDemandeUtilisateurSimple, true);
                } elseif ($form->getClickedButton()->getName() === 'rejeter') {
                    $workflow->apply($publiciteDemandeUtilisateurSimple, 'rejeter');

                    $publiciteDemandeUtilisateurSimpleRepository->save($publiciteDemandeUtilisateurSimple, true);
                } else {
                    $publiciteDemandeUtilisateurSimpleRepository->save($publiciteDemandeUtilisateurSimple, true);
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

        return $this->renderForm('publicite/publicite_demande_utilisateur_simple/edit.html.twig', [
            'publicite_demande_utilisateur_simple' => $publiciteDemandeUtilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/rejeter', name: 'app_publicite_publicite_demande_utilisateur_simple_rejeter', methods: ['GET', 'POST'])]
    public function Rejeter(Request $request, PubliciteDemandeUtilisateurSimple $publiciteDemandeUtilisateurSimple, PubliciteDemandeUtilisateurSimpleRepository $publiciteDemandeUtilisateurSimpleRepository, FormError $formError): Response
    {
        //dd();
        $form = $this->createForm(PubliciteDemandeUtilisateurSimpleType::class, $publiciteDemandeUtilisateurSimple, [
            'method' => 'POST',
            'type' => 'rejeter',
            'action' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_rejeter', [
                'code' =>  $publiciteDemandeUtilisateurSimple->getCode()
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
            $workflow = $this->workflow->get($publiciteDemandeUtilisateurSimple, 'add_demande_publicite_utilisateur_simple');

            if ($form->isValid()) {
                //dd($workflow->can($demande,'document_verification_refuse'));
                if ($workflow->can($publiciteDemandeUtilisateurSimple, 'rejeter')) {
                    $workflow->apply($publiciteDemandeUtilisateurSimple, 'rejeter');
                    $this->em->flush();
                }
                $publiciteDemandeUtilisateurSimpleRepository->save($publiciteDemandeUtilisateurSimple, true);
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
            'workflowServicePrestataire' => $publiciteDemandeUtilisateurSimple,
            // 'fichiers' => $repository->findOneBySomeFields($demande),
            'form' => $form,
        ]);
    }

    #[Route('/{code}/delete', name: 'app_publicite_publicite_demande_utilisateur_simple_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, PubliciteDemandeUtilisateurSimple $publiciteDemandeUtilisateurSimple, PubliciteDemandeUtilisateurSimpleRepository $publiciteDemandeUtilisateurSimpleRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_publicite_publicite_demande_utilisateur_simple_delete',
                    [
                        'code' => $publiciteDemandeUtilisateurSimple->getCode()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $publiciteDemandeUtilisateurSimpleRepository->remove($publiciteDemandeUtilisateurSimple, true);

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

        return $this->renderForm('publicite/publicite_demande_utilisateur_simple/delete.html.twig', [
            'publicite_demande_utilisateur_simple' => $publiciteDemandeUtilisateurSimple,
            'form' => $form,
        ]);
    }
}
