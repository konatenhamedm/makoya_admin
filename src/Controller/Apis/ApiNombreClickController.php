<?php


namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\NombreClick;
use App\Repository\NombreClickRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/visite')]
class ApiNombreClickController extends ApiInterface
{


    #[Route('/{type}/{sousCategorie}', name: 'api_visite', methods: ['GET'])]
    /**
     * Retourne la liste des retours
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OAA\JsonContent(
            type: 'array',
            items: new OAA\Items(ref: new Model(type: NombreClick::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'nombreClick')]
    // #[Security(name: 'Bearer')]
    public function getListeServiceMoreVisited($type, $sousCategorie, NombreClickRepository $nombreClickRepository)
    {
        //dd($nombreClickRepository->getAllServices($sousCategorie));
        try {

            if ($type == 'service') {
                $data = $nombreClickRepository->getAllServices($sousCategorie);
            } else {
                $data = $nombreClickRepository->getAllSousCategories();
            }
            //dd($data);
            /*  dd($services); */
            $response = $this->responseNew($data, 'groupe_commentaire');
        } catch (\Throwable $th) {
            //throw $th;
            $this->setMessage("erreur");
            $response = $this->response('[]');
        }

        return $response;
    }
    #[Route('/categorie', name: 'api_visite', methods: ['GET'])]
    /**
     * Retourne la liste des retours
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OAA\JsonContent(
            type: 'array',
            items: new OAA\Items(ref: new Model(type: NombreClick::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'nombreClick')]
    //#[Security(name: 'Bearer')]
    public function getListeCategorieVisited(NombreClickRepository $nombreClickRepository)
    {
        //dd($nombreClickRepository->getAllServices($sousCategorie));
        try {

            $response = $this->responseNew($nombreClickRepository->getAllCategories(), 'groupe_commentaire');
        } catch (\Throwable $th) {
            //throw $th;
            $this->setMessage("erreur");
            $response = $this->response('[]');
        }

        return $response;
    }
}
