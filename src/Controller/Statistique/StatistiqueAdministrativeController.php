<?php

namespace App\Controller\Statistique;

use App\Controller\BaseController;
use App\Entity\UserFront;
use App\Repository\CommuneRepository;
use App\Repository\RegionRepository;
use App\Repository\UserFrontRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\isEmpty;

class StatistiqueAdministrativeController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_statistique_administrative';

    #[Route('/ads/statistique/administrative', name: 'app_statistique_administrative')]
    public function index(UserFrontRepository $userFrontRepository): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);


        $dateDebut = [];
        $dateFin = [];
        $users = $userFrontRepository->findAll();
        $response = [];
        $dataDebut = [];
        $dataFin = [];
        $i = 0;
        foreach ($users as $user) {

            //foreach ($dateDebut as $key => $value) {

            if ($user->getDateCreation()) {
                /* if (!in_array(date_format($user->getDateCreation(), 'Y'), $dateDebut)) {
                    


                    //$dateDebut[$i][date_format($user->getDateCreation(), 'Y')] = date_format($user->getDateCreation(), 'Y');
                } */

                $dataDebut[date_format($user->getDateCreation(), 'Y')] = [
                    'key' => date_format($user->getDateCreation(), 'Y')
                ];
                //dd($data[$i]);
                array_push($dateDebut, $dataDebut);
            }

            if ($user->getDateDesactivation()) {

                $dataFin[date_format($user->getDateDesactivation(), 'Y')] = [
                    'key' => date_format($user->getDateDesactivation(), 'Y')
                ];
                //dd($data[$i]);
                array_push($dateFin, $dataFin);
            }

            if (count($dateDebut) < 0) {
                $dateFin[] = (new \DateTime())->format('Y');
            }
        }
        $response['dateDebut'] = end($dateDebut);
        $response['dateFin'] = end($dateFin);


        return $this->render('statistique_administrative/pages/test.html.twig', [
            'permition' => $permission,
            'response' => $response
        ]);
    }

    #[Route('/ads/api/liste_region', name: 'get_all_region', methods: ['GET'])]
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
        $j = 0;
        $nbre = 0;
        foreach ($dataSousCat as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
            $data = $regionRepository->getNbrePrestataireUserSimpleByRegions($e->getCode());
            // dd($data);
            /*        foreach ($data as $key => $value) {
                if(str_contains($value['reference'] , "PR")){
                    $nbre = $nbre + 1;
                }
                
                
            }  */

            $tabEnsemblesSousCate[$i]['nbre'] = count($data);
            // $tabEnsemblesSousCate[$i]['nbre'] = $nbre[0]['nbre'];
            $tabEnsemblesSousCate[$i]['id'] = $e->getCode();
            $i++;
        }
        //dd($tabEnsemblesSousCate);
        $dataSousCategorie = json_encode($tabEnsemblesSousCate); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($dataSousCategorie);

        // dd($response);
        /* } */
        return $response;
    }

    #[Route('/ads/api/infos', name: 'get_all_info', methods: ['GET'])]
    public function getClickInfos(Request $request, RegionRepository $regionRepository)
    {
        $response = new Response();
        $tabEnsemblesSousCate = array();

        $id = '';
        $region = $request->get('region');

        $reponse = [];
        $dataPrestataire = [];
        $dataP = [];
        $dataUtilisateurSimple = [];
        $dataU = [];
        $total = 0;
        $nbrePrestataire = 0;

        $nbreUtilisateurSimple = 0;
        // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
        $data = $regionRepository->getInfos($region);

        foreach ($data as $key => $value) {

            if (str_contains($value['reference'], "PR")) {
                $nbrePrestataire = $nbrePrestataire + 1;
                array_push($dataP, $value);
                $dataPrestataire['nbre'] = $nbrePrestataire;
                $dataPrestataire['titre'] = "Prestataire";
            }

            if (str_contains($value['reference'], "US")) {
                $nbreUtilisateurSimple = $nbreUtilisateurSimple + 1;
                array_push($dataU, $value);
                $dataUtilisateurSimple['nbre'] = $nbreUtilisateurSimple;
                $dataUtilisateurSimple['titre'] = "Utilisateur Simple";
            }
        }

        if (count($dataUtilisateurSimple) == 0) {
            $dataUtilisateurSimple['titre'] = "Utilisateur Simple";
        }
        if (count($dataPrestataire) == 0) {
            $dataPrestataire['titre'] = "Prestataire";
        }

        $dataUtilisateurSimple['data'] = $dataU;
        $dataPrestataire['data'] = $dataP;
        //dd($dataPrestataire, $dataUtilisateurSimple);

        $allData = [];
        array_push($allData, $dataPrestataire);
        array_push($allData, $dataUtilisateurSimple);

        $reponse['data'] = $allData;






        //dd($tabEnsemblesSousCate);
        $data = json_encode($reponse); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);

        //dd($response);
        /* } */
        return $response;
    }
    #[Route('/ads/api/liste_date', name: 'get_date', methods: ['GET'])]
    public function getYears(Request $request, UserFrontRepository $userFrontRepository)
    {
        $dateDebut = [];
        $dateFin = [];
        $users = $userFrontRepository->findAll();
        $response = [];
        $dataDebut = [];
        $dataFin = [];
        $i = 0;
        foreach ($users as $user) {

            //foreach ($dateDebut as $key => $value) {

            if ($user->getDateCreation()) {
                /* if (!in_array(date_format($user->getDateCreation(), 'Y'), $dateDebut)) {
                    


                    //$dateDebut[$i][date_format($user->getDateCreation(), 'Y')] = date_format($user->getDateCreation(), 'Y');
                } */

                $dataDebut[date_format($user->getDateCreation(), 'Y')] = [
                    'key' => date_format($user->getDateCreation(), 'Y')
                ];
                //dd($data[$i]);
                array_push($dateDebut, $dataDebut);
            }

            if ($user->getDateDesactivation()) {

                $dataFin[date_format($user->getDateDesactivation(), 'Y')] = [
                    'key' => date_format($user->getDateDesactivation(), 'Y')
                ];
                //dd($data[$i]);
                array_push($dateFin, $dataFin);
            }

            if (count($dateDebut) < 0) {
                $dateFin[] = (new \DateTime())->format('Y');
            }
        }
        $response['dateDebut'] = end($dateDebut);
        $response['dateFin'] = end($dateFin);


        // dd($response);
        return $this->json($response);
    }
}
