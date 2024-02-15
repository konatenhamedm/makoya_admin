<?php

namespace App\Controller\Sponsoring;

use App\Entity\Sponsoring;
use App\Form\SponsoringType;
use App\Repository\SponsoringRepository;
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
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[Route('/ads/sponsoring/sponsoring')]
class SponsoringController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_sponsoring_sponsoring_index';

    #[Route('/', name: 'app_sponsoring_sponsoring_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $datetime = new \DateTime("now");

        /*  $niveau = $request->query->get('niveau');*/
        $etat = $request->query->get('etat');
        $type = $request->query->get('type');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        //$mode = $request->query->get('mode');


        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_sponsoring_sponsoring_index', compact('dateDebut', 'dateFin', 'type', 'etat'))
        ])

            ->add(
                'etat',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un etat',
                    'label' => 'Etat',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'EN_COURS' => 'En cours',
                        'PRESQUE' => 'Presque terminer',
                        'TERMINER' => 'Terminer',
                        'demande_initie' => 'Demande initie',
                        'demande_valider' => 'demande valider',
                        'demande_rejeter' => 'demande rejeter',
                    ]),
                ]
            )

            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ]);


        $table = $dataTableFactory->create()


            ->add('entreprise', TextColumn::class, ['label' => 'Entreprise'])
            ->add('contact', TextColumn::class, ['label' => 'Contact'])
            ->add('titre', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('etatV', TextColumn::class, ['className' => 'w-50px', 'field' => 'l.id', 'label' => 'Etat', 'render' => function ($value, Sponsoring $context) use ($datetime) {
                $diff_in_days = floor((strtotime($context->getDateFin()->format("Y-m-d")) - strtotime($datetime->format("Y-m-d"))) / (60 * 60 * 24));

                //dd($diff_in_days);

                if ($context->getEtat() == 'demande_valider') {
                    if (intval($diff_in_days) == 0  || intval($diff_in_days) < 0) {
                        $label = 'Terminer';
                        $color = 'danger';
                    } elseif (intval($diff_in_days) < 8 && intval($diff_in_days)  > 0) {
                        $label = 'Presque';
                        $color = 'warning';
                    } else {
                        $label = 'En cours';
                        $color = 'success';
                    }
                } elseif ($context->getEtat() == 'demande_rejeter') {
                    $label = 'Rejetée';
                    $color = 'danger';
                } else {
                    $label = 'Attente validataion';
                    $color = 'primary';
                }

                return sprintf('<span class="badge badge-%s">%s</span>', $color, $label);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Sponsoring::class,
                'query' => function (QueryBuilder $qb) use ($dateDebut, $dateFin, $etat, $datetime) {
                    $qb->select('e')
                        ->from(Sponsoring::class, 'e');
                    /*   ->andWhere('e.code LIKE :cat or e.code LIKE :rgp or e.code LIKE :enc')
                        ->setParameter('cat', '%CAT%')
                        ->setParameter('rgp', '%RGP%')
                        ->setParameter('enc', '%ENC%'); */
                    /* ->leftJoin('e.utilisateur', 'u'); */

                    if ($dateDebut || $dateFin || $etat) {



                        if ($etat) {

                            // dd($etat);
                            if (in_array($etat, ['TERMINER', 'PRESQUE', 'EN_COURS'])) {
                                if ($etat == "TERMINER") {
                                    $qb->andWhere('e.dateFin = :current_date or DATE_DIFF(e.dateFin, :current_date) < 0')
                                        ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                                } elseif ($etat == "PRESQUE") {
                                    $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) < 8 and  DATE_DIFF(e.dateFin, :current_date) > 0")
                                        ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                                } else {
                                    $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) >= 8 ")
                                        ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                                }
                            } else {

                                $qb->andWhere("e.etat = :etat")
                                    ->setParameter('etat', $etat);
                            }
                        }



                        if ($dateDebut) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin) {
                            //date_format($date, 'Y-m-d H:i:s')2024-02-02 10:54:11;
                            $truc = explode('-', str_replace("/", "-", $dateFin));

                            //  dd($truc);
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {
                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0] . ' ' . '00:00:00';

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0] . ' ' . '00:00:00';

                            $qb->andWhere('e.dateFin BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }


                        if ($etat) {

                            if ($etat == "TERMINER") {
                                $qb->andWhere('e.dateFin = :current_date or DATE_DIFF(e.dateFin, :current_date) < 0')
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } elseif ($etat == "PRESQUE") {
                                $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) < 8 and  DATE_DIFF(e.dateFin, :current_date) > 0")
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } elseif ($etat == "EN_COURS") {
                                $qb->andWhere(" DATE_DIFF(e.dateFin, :current_date) >= 8 ")
                                    ->setParameter('current_date', new \DateTime($datetime->format("Y-m-d")));
                            } elseif ($etat == "ATTENTE") {
                                $qb->andWhere('e.etat  = :attente')
                                    ->setParameter('attente', 'demande_initie');
                            }
                        }
                    }
                }
            ])
            ->setName('dt_app_sponsoring_sponsoring_' . $etat);
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

            $gridId =  $etat;
            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Sponsoring $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_sponsoring_sponsoring_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_sponsoring_sponsoring_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_sponsoring_sponsoring_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('sponsoring/sponsoring/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'form' => $builder->getForm()->createView(),
            'grid_id' => $gridId,

        ]);
    }

    #[Route('/new', name: 'app_sponsoring_sponsoring_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, NotificationService $notificationService): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $sponsoring = new Sponsoring();
        $form = $this->createForm(SponsoringType::class, $sponsoring, [
            'method' => 'POST',
            'type' => 'allData',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_sponsoring_sponsoring_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_sponsoring_sponsoring_index');

            $user = $form->get('utilisateur')->getData();

            if ($form->isValid()) {
                $sponsoring->setDateCreation(new \DateTime());

                if ($form->getClickedButton()->getName() === 'passer') {
                    $sponsoring->setEtat('demande_valider');
                    $sponsoring->setDateValidation(new \DateTime());


                    $mess = sprintf('%s  votre deamdne de sponsoring a été validée avec success', $user->getNom() . ' ' . $user->getPrenom());


                    if ($user) {
                        $notificationService->sendNotification($mess, 'Message de validation', $user->getId());
                    }
                } else {
                    $sponsoring->setEtat('demande_initie');
                }




                $entityManager->persist($sponsoring);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
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

        return $this->renderForm('sponsoring/sponsoring/new.html.twig', [
            'sponsoring' => $sponsoring,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_sponsoring_sponsoring_show', methods: ['GET'])]
    public function show(Sponsoring $sponsoring): Response
    {
        return $this->render('sponsoring/sponsoring/show.html.twig', [
            'sponsoring' => $sponsoring,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sponsoring_sponsoring_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsoring $sponsoring, EntityManagerInterface $entityManager, FormError $formError, NotificationService $notificationService): Response
    {

        dd($_SERVER['REMOTE_HOST']);
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(SponsoringType::class, $sponsoring, [
            'method' => 'POST',
            'type' => 'allData',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_sponsoring_sponsoring_edit', [
                'id' => $sponsoring->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_sponsoring_sponsoring_index');
            $user = $form->get('utilisateur')->getData();

            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'passer') {
                    $sponsoring->setEtat('demande_valider');
                    $sponsoring->setDateValidation(new \DateTime());


                    //dd($user->getUsername());

                    if ($user) {

                        $mess       = sprintf('votre demande de sponsoring a été validée avec success %s', 'fffff');
                        //  $mess = printf('%s  votre demande de sponsoring a été validée avec success', $user->getUsername());
                        $notificationService->sendNotification($mess, 'Message de validation sponsoring', $user);
                    }
                }

                $entityManager->persist($sponsoring);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
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

        return $this->renderForm('sponsoring/sponsoring/edit.html.twig', [
            'sponsoring' => $sponsoring,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/rejeter', name: 'app_sponsoring_sponsoring_rejeter', methods: ['GET', 'POST'])]
    public function rejeter(Request $request, Sponsoring $sponsoring, EntityManagerInterface $entityManager, FormError $formError, NotificationService $notificationService): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(SponsoringType::class, $sponsoring, [
            'method' => 'POST',
            'type' => 'non',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_sponsoring_sponsoring_rejeter', [
                'id' => $sponsoring->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_sponsoring_sponsoring_index');
            $user = $sponsoring->getUtilisateur();

            if ($form->isValid()) {


                $sponsoring->setEtat('demande_rejeter');
                $sponsoring->setDateValidation(new \DateTime());



                if ($user) {
                    $mess = sprintf('%s  votre demande de sponsoring a été rejeté', $user->getUsername());
                    $notificationService->sendNotification($mess, 'Message de validation sponsoring', $user);
                }


                $entityManager->persist($sponsoring);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
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

        return $this->renderForm('sponsoring/sponsoring/rejeter.html.twig', [
            'sponsoring' => $sponsoring,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sponsoring_sponsoring_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Sponsoring $sponsoring, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_sponsoring_sponsoring_delete',
                    [
                        'id' => $sponsoring->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($sponsoring);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_sponsoring_sponsoring_index');

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

        return $this->renderForm('sponsoring/sponsoring/delete.html.twig', [
            'sponsoring' => $sponsoring,
            'form' => $form,
        ]);
    }
}
