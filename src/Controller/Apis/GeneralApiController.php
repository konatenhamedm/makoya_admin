<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Commentaire;
use App\Entity\PrestataireService;
use App\Entity\Quartier;
use App\Entity\SousCategorie;
use App\Repository\CategorieRepository;
use App\Repository\CiviliteRepository;
use App\Repository\CommentaireRepository;
use App\Repository\CommuneRepository;
use App\Repository\NoteRepository;
use App\Repository\PharmacieRepository;
use App\Repository\PrestataireServiceRepository;
use App\Repository\QuartierRepository;
use App\Repository\UserFrontRepository;
use App\Repository\UtilisateurSimpleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\String\toString;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api/general')]
class GeneralApiController extends ApiInterface
{


    #[Route('/', name: 'api_general', methods: ['GET'])]
    /**
     * Affiche toutes les quartiers optimise.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Quartier::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function getQuartiers(QuartierRepository $quartierRepository, CiviliteRepository $civiliteRepository, CategorieRepository $categorieRepository, CommuneRepository $communeRepository): Response
    {

        $quartiers = $quartierRepository->getQuartiers();
        $civilites = $civiliteRepository->getCivilites();
        $categories = $categorieRepository->getCategories();
        $villes = $communeRepository->getCommunes();
        //dd($categories);
        $tabQuartier = [];
        $tabCivilite = [];
        $tabCategorie = [];
        $tabVille = [];
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        foreach ($quartiers as $value) {

            $tabQuartier[$i]['id'] = $value['id'];
            $tabQuartier[$i]['nom'] = $value['nom'];
            $i++;
        }
        foreach ($civilites as $value) {

            $tabCivilite[$j]['id'] = $value['id'];
            $tabCivilite[$j]['libelle'] = $value['libelle'];
            $j++;
        }
        foreach ($categories as $value) {

            $tabCategorie[$k]['id'] = $value['id'];
            $tabCategorie[$k]['libelle'] = $value['libelle'];
            //    . $utilisateur->getPhoto()->getFileNamePath()
            $tabCategorie[$k]['imageLaUne'] = [
                'fileNamePath' =>  $value['path'] . '/' . $value['alt']
            ];
            $k++;
        }
        foreach ($villes as $value) {

            $tabVille[$l]['id'] = $value['id'];
            $tabVille[$l]['libelle'] = $value['nom'];
            $l++;
        }


        $response = [
            "quartiers" => $tabQuartier,
            "civilites" => $tabCivilite,
            "categories" => $tabCategorie,
            "villes" => $tabVille,
        ];


        return $this->json([
            'data' => $response,

        ], 200);
    }


    #[Route('/services/{id}', name: 'api_gener_service', methods: ['GET'])]
    /**
     * Affiche toutes les service optimise.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Quartier::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="PrestataireService")
     * @Security(name="Bearer")
     */
    public function getService($id, PrestataireServiceRepository $prestataireServiceRepository, SousCategorie $sousCategorie, NoteRepository $noteRepository, QuartierRepository $quartierRepository, UserFrontRepository $utilisateurSimpleRepository): Response
    {
        $services = $prestataireServiceRepository->getServices($id);


        // dd($noteRepository->noteSerice(5));
        $tabService = [];

        $k = 0;
        $countNombre = 0;

        /*  export type Details = {
        id: number;
        countVisite: number;
        libelle: string;
        categorie: {
          id: number;
          libelle: string;
        };
        sousCategorie: {
          id: number;
          libelle: string;
        };
        prestataire: {
          id: number;
          denominationSociale: string;
          contactPrincipal: string;
        };
        service: {
          id: number;
          libelle: string;
        };
        imageLaUne: {
          fileNamePath: string;
        };
      }; */

        foreach ($services as $value) {

            $tabService[$k]['id'] = $value['id'];
            $tabService[$k]['note'] = $noteRepository->noteSerice($value['id']);
            $tabService[$k]['countVisite'] = $value['countVisite'];
            //    . $utilisateur->getPhoto()->getFileNamePath()
            $tabService[$k]['image'] = [
                'fileNamePath' =>  $value['path'] . '/' . $value['alt']
            ];
            $tabService[$k]['sousCategorie'] = [
                'id' =>  $value['sId'],
                'libelle' =>  $value['sousCategorie'],
            ];
            $tabService[$k]['prestataire'] = [
                'id' =>  $value['pId'],
                'denominationSociale' =>  $value['denominationSociale'],
                'contactPrincipal' => substr(strrev(trim(chunk_split(strrev($value['contactPrincipal']), 2, '-'))), 1),
                'statut' =>  $value['statut'],
                'quartier' => $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getCommune()->getNom() . ' - ' .  $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getNom(),
            ];
            $tabService[$k]['service'] = [
                'id' =>  $value['serId'],
                'libelle' =>  $value['service'],
            ];

            $k++;
        }

        $response = [
            "services" => $tabService,
            'sousCategories' => $sousCategorie->getCategorie()->getLibelle() . ' / ' . $sousCategorie->getLibelle(),

        ];
        return $this->json([
            'data' => $response,

        ], 200);
    }

    #[Route('/services_all', name: 'api_gener_service_all', methods: ['GET'])]
    /**
     * Affiche toutes les service optimise.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Quartier::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="PrestataireService")
     * @Security(name="Bearer")
     */
    public function getServiceAll(PrestataireServiceRepository $prestataireServiceRepository, NoteRepository $noteRepository, QuartierRepository $quartierRepository, UserFrontRepository $utilisateurSimpleRepository): Response
    {
        $services = $prestataireServiceRepository->getServicesAll();


        // dd($noteRepository->noteSerice(5));
        $tabService = [];

        $k = 0;
        $countNombre = 0;

        /*  export type Details = {
        id: number;
        countVisite: number;
        libelle: string;
        categorie: {
          id: number;
          libelle: string;
        };
        sousCategorie: {
          id: number;
          libelle: string;
        };
        prestataire: {
          id: number;
          denominationSociale: string;
          contactPrincipal: string;
        };
        service: {
          id: number;
          libelle: string;
        };
        imageLaUne: {
          fileNamePath: string;
        };
      }; */

        foreach ($services as $value) {

            $tabService[$k]['id'] = $value['id'];
            $tabService[$k]['note'] = $noteRepository->noteSerice($value['id']);
            $tabService[$k]['countVisite'] = $value['countVisite'];
            //    . $utilisateur->getPhoto()->getFileNamePath()
            $tabService[$k]['image'] = [
                'fileNamePath' =>  $value['path'] . '/' . $value['alt']
            ];
            $tabService[$k]['sousCategorie'] = [
                'id' =>  $value['sId'],
                'libelle' =>  $value['sousCategorie'],
            ];
            $tabService[$k]['prestataire'] = [
                'id' =>  $value['pId'],
                'denominationSociale' =>  $value['denominationSociale'],
                'contactPrincipal' => substr(strrev(trim(chunk_split(strrev($value['contactPrincipal']), 2, '-'))), 1),
                'statut' =>  $value['statut'],
                'quartier' => $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getCommune()->getNom() . ' - ' .  $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getNom(),
            ];
            $tabService[$k]['service'] = [
                'id' =>  $value['serId'],
                'libelle' =>  $value['service'],
            ];

            $k++;
        }

        $response = [
            "services" => $tabService,
            'sousCategories' => "dddd",

        ];
        return $this->json([
            'data' => $response,

        ], 200);
    }



    #[Route('/retour_experience', name: 'api_retour_experience', methods: ['GET'])]
    /**
     * Affiche toutes les service optimise.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Quartier::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="PrestataireService")
     * @Security(name="Bearer")
     */
    public function getRetourExperience(
        PrestataireServiceRepository $prestataireServiceRepository,
        NoteRepository $noteRepository,
        QuartierRepository $quartierRepository,
        UserFrontRepository $utilisateurSimpleRepository,
        CommentaireRepository $commentaireRepository,
        PharmacieRepository $pharmacieRepository
    ): Response {
        $services = $prestataireServiceRepository->getServicesAllC();
        //$pharmacies = $pharmacieRepository->findAll();


        // dd($noteRepository->noteSerice(5));
        $tabService = [];
        $tablePharmacie = [];

        $k = 0;


        foreach ($services as $value) {

            $tabService[$k]['id'] = $value['id'];
            $tabService[$k]['note'] = $noteRepository->noteSerice($value['id']);
            $tabService[$k]['countVisite'] = $value['countVisite'];
            $tabService[$k]['message'] = $value['message'];
            //    . $utilisateur->getPhoto()->getFileNamePath()
            $tabService[$k]['image'] = [
                'fileNamePath' =>  $value['path'] . '/' . $value['alt']
            ];
            $tabService[$k]['sousCategorie'] = [
                'id' =>  $value['sId'],
                'libelle' =>  $value['sousCategorie'],
            ];
            $tabService[$k]['prestataire'] = [
                'id' =>  $value['pId'],
                'denominationSociale' =>  $value['denominationSociale'],
                'contactPrincipal' => substr(strrev(trim(chunk_split(strrev($value['contactPrincipal']), 2, '-'))), 1),
                'statut' =>  $value['statut'],
                'quartier' => $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getCommune()->getNom() . ' - ' .  $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getNom(),
                'quartierService' => $quartierRepository->find($utilisateurSimpleRepository->find($value['pId'])->getQuartier())->getCommune()->getNom() . ' - ' . $value['service'],
            ];
            $tabService[$k]['service'] = [
                'id' =>  $value['serId'],
                'libelle' =>  $value['service'],
            ];

            $k++;
        }

        $response = [
            "services" => $tabService,
            'sousCategories' => "dddd",
            //'pharmacie' => $tablePharmacie,

        ];
        return $this->json([
            'data' => $response,

        ], 200);
    }
}
