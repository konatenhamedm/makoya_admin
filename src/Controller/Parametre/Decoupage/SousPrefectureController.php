<?php

namespace App\Controller\Parametre\Decoupage;

use App\Entity\SousPrefecture;
use App\Form\SousPrefectureType;
use App\Repository\SousPrefectureRepository;
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

#[Route('/ads/parametre/decoupage/sous/prefecture')]
class SousPrefectureController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_parametre_decoupage_sous_prefecture_index';

    #[Route('/ads/', name: 'app_parametre_decoupage_sous_prefecture_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('nom', TextColumn::class, ['label' => 'Nom'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => SousPrefecture::class,
            ])
            ->setName('dt_app_parametre_decoupage_sous_prefecture');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, SousPrefecture $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_parametre_decoupage_sous_prefecture_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_parametre_decoupage_sous_prefecture_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_decoupage_sous_prefecture_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
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


        return $this->render('parametre/decoupage/sous_prefecture/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }
    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(SousPrefecture::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'SPT' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    #[Route('/ads/new', name: 'app_parametre_decoupage_sous_prefecture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SousPrefectureRepository $sousPrefectureRepository, FormError $formError): Response
    {
        $sousPrefecture = new SousPrefecture();
        $form = $this->createForm(SousPrefectureType::class, $sousPrefecture, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_decoupage_sous_prefecture_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_decoupage_sous_prefecture_index');


            if ($form->isValid()) {
                $sousPrefecture->setCode($this->numero());
                $sousPrefectureRepository->save($sousPrefecture, true);
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

        return $this->renderForm('parametre/decoupage/sous_prefecture/new.html.twig', [
            'sous_prefecture' => $sousPrefecture,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/show', name: 'app_parametre_decoupage_sous_prefecture_show', methods: ['GET'])]
    public function show(SousPrefecture $sousPrefecture): Response
    {
        return $this->render('parametre/decoupage/sous_prefecture/show.html.twig', [
            'sous_prefecture' => $sousPrefecture,
        ]);
    }

    #[Route('/ads/{id}/edit', name: 'app_parametre_decoupage_sous_prefecture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SousPrefecture $sousPrefecture, SousPrefectureRepository $sousPrefectureRepository, FormError $formError): Response
    {

        $form = $this->createForm(SousPrefectureType::class, $sousPrefecture, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_decoupage_sous_prefecture_edit', [
                'id' =>  $sousPrefecture->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_decoupage_sous_prefecture_index');


            if ($form->isValid()) {

                $sousPrefectureRepository->save($sousPrefecture, true);
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

        return $this->renderForm('parametre/decoupage/sous_prefecture/edit.html.twig', [
            'sous_prefecture' => $sousPrefecture,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{id}/delete', name: 'app_parametre_decoupage_sous_prefecture_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, SousPrefecture $sousPrefecture, SousPrefectureRepository $sousPrefectureRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_decoupage_sous_prefecture_delete',
                    [
                        'id' => $sousPrefecture->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $sousPrefectureRepository->remove($sousPrefecture, true);

            $redirect = $this->generateUrl('app_parametre_decoupage_sous_prefecture_index');

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

        return $this->renderForm('parametre/decoupage/sous_prefecture/delete.html.twig', [
            'sous_prefecture' => $sousPrefecture,
            'form' => $form,
        ]);
    }
}
