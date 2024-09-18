<?php


namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Pharmacie;
use App\Repository\PharmacieRepository;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pharmacie')]
class ApiPharmacie extends ApiInterface
{

    #[Route('/', methods: ['GET'])]
    /**
     * Retourne la liste des pharmacies
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OAA\JsonContent(
            type: 'array',
            items: new OAA\Items(ref: new Model(type: Pharmacie::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'pharmacie')]
    //#[Security(name: 'Bearer')]
    public function getListeCategorieVisited(PharmacieRepository $pharmacieRepository)
    {
        //  dd($pharmacieRepository->findAll());
        try {

            $response = $this->responseNew($pharmacieRepository->findAll(), 'groupe_commentaire');
        } catch (\Throwable $th) {
            //throw $th;
            $this->setMessage("erreur");
            $response = $this->response('[]');
        }

        return $response;
    }
}
