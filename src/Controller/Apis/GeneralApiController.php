<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Quartier;
use App\Repository\CategorieRepository;
use App\Repository\CiviliteRepository;
use App\Repository\CommuneRepository;
use App\Repository\QuartierRepository;
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
}
