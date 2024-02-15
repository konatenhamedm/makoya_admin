<?php

namespace App\Controller\Configuration;

use App\Controller\BaseController;
use App\Repository\CiviliteRepository;
use App\Service\Breadcrumb;
use App\Service\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/ads/admin/config/workflow')]
class WorkflowServicePrestataireController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_config_workflow_index';

    #[Route(path: '/', name: 'app_config_workflow_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);


        $modules = [
            [
                'label' => 'Workflow service prestataire',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_workflow_ls', ['module' => 'workflow_service'])
            ],
            [
                'label' => 'Messagerie',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_workflow_ls', ['module' => 'messagerie'])
            ],
            [
                'label' => 'Signalement',
                'icon' => 'bi bi-people',
                'href' => $this->generateUrl('app_resaux_signaler_index')
            ],
            [
                'label' => 'Reclamation',
                'icon' => 'bi bi-people',
                'href' => $this->generateUrl('app_config_workflow_ls', ['module' => 'reclamation'])
            ],
            /*  [
                'label' => 'Demande service',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_workflow_ls', ['module' => 'demande'])
            ], */
            [
                'label' => 'Proposition service',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_workflow_ls', ['module' => 'proposition'])
            ]



        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);

        return $this->render('config/workflow/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'permition' => $permission
        ]);
    }


    #[Route(path: '/{module}', name: 'app_config_workflow_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [



            'workflow_service' => [
                [
                    'label' => 'En attente validation',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_index', ['etat' => 'demande_initie'])
                ],
                [
                    'label' => 'validées',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_index', ['etat' => 'demande_valider'])
                ],
                [
                    'label' => 'Rejetées',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_workflowdemande_workflow_service_prestataire_index', ['etat' => 'demande_rejeter'])
                ]


            ],

            'proposition' => [
                [
                    'label' => 'En attente validation',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_parametre_prestation_proposition_service_index', ['etat' => 'proposition_initie'])
                ],
                [
                    'label' => 'validées',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_parametre_prestation_proposition_service_index', ['etat' => 'proposition_valider'])
                ],
                [
                    'label' => 'Rejetées',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_parametre_prestation_proposition_service_index', ['etat' => 'proposition_rejeter'])
                ]


            ],
            'messagerie' => [
                [
                    'label' => 'Contact',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_message_contact_index')
                ],
                [
                    'label' => 'Newsletter',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_message_newsletter_index')
                ],
                [
                    'label' => 'Type Faqs',
                    'id' => 'param_cm_type_faq',
                    'href' => $this->generateUrl('app_message_type_faqs_index')
                ],
                [
                    'label' => 'Faqs',
                    'id' => 'param_cm_faq',
                    'href' => $this->generateUrl('app_message_faqs_index')
                ],



            ],
            'reclamation' => [
                [
                    'label' => 'En cours de traitement',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_parametre_reclamation_index', ['etat' => 'en_cours'])
                ],
                [
                    'label' => 'Traité',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_parametre_reclamation_index', ['etat' => 'traiter'])
                ],



            ],


        ];


        return $this->render('config/workflow/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
