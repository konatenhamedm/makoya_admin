<?php

namespace App\Controller\Statistique;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/statistque/dashboard')]
class DashboardController extends BaseController
{
    
    #[Route('/', name: 'app_president_dashboard_index')]
    public function index(): Response
    {
       
            $modules = [
                [
                    'label' => 'Evolution demandes par sexe et entreprise',
                    'id' => 'chart_one',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Evolution demandes par mois et entreprise',
                    'id' => 'chart_two',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Evolution demandes par sexe',
                    'id' => 'chart_tree',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Evolution demandes années et par entreprise',
                    'id' => 'chart_four',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Evolution demandes par motifs et année et entreprise',
                    'id' => 'chart_py_age',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Demande par motif et entreprise',
                    'id' => 'chart_py_anc',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Demandes par entreprise',
                    'id' => 'chart_maitrise',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],

                [
                    'label' => 'Comparaison homme contre femme',
                    'id' => 'chart_compraison',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                [
                    'label' => 'Classement par nombre',
                    'id' => 'chart_classement',
                    'icon' => 'bi bi-list',
                    'href' => $this->generateUrl('app_statistique_action')
                ],
                //

            ];
       

        return $this->render('statistique_administrative/dashboard.html.twig', [
            'modules' => $modules,
            'titre' => "Dashboard",
        ]);
    }


    
    #[Route('/action', name: 'app_statistique_action')]
    public function indexAction(): Response
    {

       
        return $this->render('statistique_administrative/pages/index.html.twig', [
           
        ]);
    }


}
