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

#[Route('/ads/admin/config/publicite')]
class GestionPubliciteController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_config_publicite_index';

    #[Route(path: '/', name: 'app_config_publicite_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);


        $modules = [


            [
                'label' => 'Pubs catégorie',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_publicite_ls', ['module' => 'categorie'])
            ],
            [
                'label' => 'Pubs région',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_publicite_ls', ['module' => 'region'])
            ],
            [
                'label' => 'Pubs encart',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_publicite_ls', ['module' => 'encart'])
            ],
            [
                'label' => 'Demande pubs prestataire',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_publicite_ls', ['module' => 'demande'])
            ],
            [
                'label' => 'Demande pubs utilisateur simple',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_publicite_ls', ['module' => 'demande_utilisateur_simple'])
            ],
            [
                'label' => 'Suivi publicités',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_publicite_ls', ['module' => 'suivi'])
            ],




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

        return $this->render('config/publicite/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'permition' => $permission
        ]);
    }


    #[Route(path: '/{module}', name: 'app_config_publicite_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [


            'categorie' => [
                [
                    'label' => 'En cours',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_categorie_index', ['etat' => 'all'])
                ],
                [
                    'label' => 'Presque terminée',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_categorie_index', ['etat' => 'en_cours_peremption'])
                ],
                [
                    'label' => 'Echues',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_categorie_index', ['etat' => 'terminer'])
                ]


            ],
            'region' => [
                [
                    'label' => 'En cours',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_region_index', ['etat' => 'all'])
                ],
                [
                    'label' => 'Presque finie',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_region_index', ['etat' => 'en_cours_peremption'])
                ],
                [
                    'label' => 'Echues',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_region_index', ['etat' => 'terminer'])
                ]


            ],
            'prestataire' => [
                [
                    'label' => 'En cours',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_prestataire_index', ['etat' => 'all'])
                ],
                [
                    'label' => 'Presque terminée',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_prestataire_index', ['etat' => 'en_cours_peremption'])
                ],
                [
                    'label' => 'Echues',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_prestataire_index', ['etat' => 'terminer'])
                ]


            ],
            'encart' => [
                [
                    'label' => 'En cours',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_encart_index', ['etat' => 'all'])
                ],
                [
                    'label' => 'Presque terminée',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_encart_index', ['etat' => 'en_cours_peremption'])
                ],
                [
                    'label' => 'Echues',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_encart_index', ['etat' => 'terminer'])
                ]


            ],
            'suivi' => [
                [
                    'label' => 'En cours',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_prestataire_index', ['etat' => 'all'])
                ],
                [
                    'label' => 'Presque terminée',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_prestataire_index', ['etat' => 'en_cours_peremption'])
                ],
                [
                    'label' => 'Echues',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_prestataire_index', ['etat' => 'terminer'])
                ]
            ],
            'demande' => [
                [
                    'label' => 'En attente validation',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_demande_index', ['etat' => 'demande_initie'])
                ],
                [
                    'label' => 'validées',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_demande_index', ['etat' => 'demande_valider'])
                ],
                [
                    'label' => 'Rejetées',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_demande_index', ['etat' => 'demande_rejeter'])
                ]


            ],
            'demande_utilisateur_simple' => [
                [
                    'label' => 'En attente validation',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_index', ['etat' => 'demande_initie'])
                ],
                [
                    'label' => 'validées',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_index', ['etat' => 'demande_valider'])
                ],
                [
                    'label' => 'Rejetées',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_publicite_publicite_demande_utilisateur_simple_index', ['etat' => 'demande_rejeter'])
                ]
            ],


        ];


        return $this->render('config/publicite/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
