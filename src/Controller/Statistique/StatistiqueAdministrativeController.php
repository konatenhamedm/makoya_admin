<?php

namespace App\Controller\Statistique;

use App\Controller\BaseController;
use App\Repository\CommuneRepository;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueAdministrativeController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_statistique_administrative';

    #[Route('/statistique/administrative', name: 'app_statistique_administrative')]
    public function index(): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        return $this->render('statistique_administrative/pages/index.html.twig', [
            'permition' => $permission,
        ]);
    }

    #[Route('/api/liste_region', name: 'get_all_region', methods: ['GET'])]
    public function getInfoSerie(Request $request, RegionRepository $regionRepository, CommuneRepository $communeRepository)
    {
        $response = new Response();
        $tabEnsemblesSousCate = array();

        $id = '';
        $id = $request->get('intitule');
        //dd( $id);
        /*   if ($id) { */

        //dd($communeRepository->findOneBySomeField(4));

        $dataSousCat = $regionRepository->findAll();
        $i = 0;
        foreach ($dataSousCat as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
            $nbre = $regionRepository->getNbrePrestataireByRegions($e->getCode());

            $tabEnsemblesSousCate[$i]['nbre'] = $nbre[0]['nbre'];
            $tabEnsemblesSousCate[$i]['id'] = $e->getCode();
            $i++;
        }

        $dataSousCategorie = json_encode($tabEnsemblesSousCate); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($dataSousCategorie);

        // dd($response);
        /* } */
        return $response;
    }
}
