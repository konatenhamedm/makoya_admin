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

#[Route('/ads/config/parametre')]
class ParametreController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_config_parametre_index';

    #[Route(path: '/', name: 'app_config_parametre_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);


        $modules = [
            [
                'label' => 'Général',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'config'])
            ],
            [
                'label' => 'Prestation makoya',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'prestation'])
            ],
            [
                'label' => 'Ressource humaine',
                'icon' => 'bi bi-people',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'rh'])
            ],
            [
                'label' => 'Gestion utilisateur',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'utilisateur'])
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

        return $this->render('config/parametre/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'permition' => $permission
        ]);
    }


    #[Route(path: '/{module}', name: 'app_config_parametre_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [

            'prestation' => [

                [
                    'label' => 'Catégorie',
                    'id' => 'param_groupe_m',
                    'href' => $this->generateUrl('app_parametre_prestation_categorie_index')
                ],
                [
                    'label' => 'Sous categorie',
                    'id' => 'param_module',
                    'href' => $this->generateUrl('app_parametre_prestation_sous_categorie_index')
                ],
                [
                    'label' => 'Service',
                    'id' => 'param_permission',
                    'href' => $this->generateUrl('app_parametre_prestation_service_prestataire_index')
                ]/* ,
                [
                    'label' => 'Proposition service',
                    'id' => 'param_permission_groupe',
                    'href' => $this->generateUrl('app_parametre_prestation_proposition_service_index')
                ] */

            ],
            'utilisateur' => [

                [
                    'label' => 'Groupes modules',
                    'id' => 'param_groupe_m',
                    'href' => $this->generateUrl('app_utilisateur_groupe_module_index')
                ],
                [
                    'label' => 'Module',
                    'id' => 'param_module',
                    'href' => $this->generateUrl('app_utilisateur_module_index')
                ],
                [
                    'label' => 'Permissions',
                    'id' => 'param_permission',
                    'href' => $this->generateUrl('app_utilisateur_permition_index')
                ],
                [
                    'label' => 'Les elements du menu',
                    'id' => 'param_permission_groupe',
                    'href' => $this->generateUrl('app_utilisateur_module_groupe_permition_index')
                ]

            ],
            'rh' => [
                [
                    'label' => 'Fonction',
                    'id' => 'param_categorie',
                    'href' => $this->generateUrl('app_parametre_fonction_index')
                ],
                [
                    'label' => 'Direction',
                    'id' => 'param_direction',
                    'href' => $this->generateUrl('app_parametre_service_index')
                ],

                [
                    'label' => 'Employé',
                    'id' => 'param_client',
                    'href' => $this->generateUrl('app_utilisateur_employe_index')
                ],
                /*  [
                    'label' => 'Fournisseur',
                    'id' => 'param_fournisseur',
                    'href' => $this->generateUrl('app_rh_fournisseur_index')
                ],*/


            ],

            'config' => [
                [
                    'label' => 'Genre',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_parametre_civilite_index')
                ],
                [
                    'label' => 'Icons',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_parametre_icon_index')
                ],
                [
                    'label' => 'Configuration application',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_parametre_config_app_index')
                ],
                [
                    'label' => 'Pays',
                    'id' => 'param_pays',
                    'href' => $this->generateUrl('app_parametre_decoupage_pays_index')
                ],
                [
                    'label' => 'Régions',
                    'id' => 'param_region',
                    'href' => $this->generateUrl('app_parametre_decoupage_region_index')
                ],
                [
                    'label' => 'Departements',
                    'id' => 'param_departement',
                    'href' => $this->generateUrl('app_parametre_decoupage_departement_index')
                ],
                [
                    'label' => 'Sous-prefectures',
                    'id' => 'param_sp',
                    'href' => $this->generateUrl('app_parametre_decoupage_sous_prefecture_index')
                ],
                [
                    'label' => 'Communes',
                    'id' => 'param_commune',
                    'href' => $this->generateUrl('app_parametre_decoupage_commune_index')
                ],
                [
                    'label' => 'Quartiers',
                    'id' => 'param_quartier',
                    'href' => $this->generateUrl('app_parametre_decoupage_quartier_index')
                ],

            ],


        ];


        return $this->render('config/parametre/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
