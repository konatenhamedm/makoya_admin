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
use App\Repository\ServicePrestataireRepository;
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

#[Route('/api/service/prestataire')]
class ApiServicePrestataireController extends ApiInterface
{


    #[Route('/', name: 'api_service_prestataire', methods: ['GET'])]
    /**
     * Affiche toutes les quartiers optimise.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=ServicePrestataire::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="ServicePrestataire")
     * @Security(name="Bearer")
     */
    public function getData(ServicePrestataireRepository $servicePrestataireRepository): Response
    {

        $dataVisite = $servicePrestataireRepository->getServicePlusVisite('23CAT10006');


        $tabService = [];
        $i = 0;
        foreach ($dataVisite as $key => $value) {
            $tabService[$i]['service_id'] = $value['service_id'];
            $tabService[$i]['image'] = [
                'fileNamePath' =>  $value['image']
            ];

            $i++;
        }


        $response = [
            "service" => $tabService,

        ];


        return $this->json([
            'data' => $response,

        ], 200);
    }
}
