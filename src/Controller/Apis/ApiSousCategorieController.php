<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\SousCategorie;
use App\Repository\SousCategorieRepository;
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

#[Route('/api/sousCategorie')]
class ApiSousCategorieController extends ApiInterface
{
    #[Route('/', name: 'api_sousCategorie', methods: ['GET'])]
    /**
     * Affiche toutes les civiltes.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SousCategorie::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function getAll(SousCategorieRepository $sousCategorieRepository): Response
    {
        try {

            $sousCategories = $sousCategorieRepository->findAll();
            $response = $this->response($sousCategories);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }

    #[Route('/sous_categories/{id}', name: 'api_sousCategorie_by_categorie_id', methods: ['GET'])]
    public function getSousCategoriesByCategorieId($id, SousCategorieRepository $sousCategorieRepository): Response
    {
        /* try { */

        $sousCategories = $sousCategorieRepository->getSousCategorie($id);
        $tabSousCategorie = [];

        $k = 0;

        foreach ($sousCategories as $value) {
            $tabSousCategorie[$k]['id'] = $value['id'];
            $tabSousCategorie[$k]['libelle'] = $value['libelle'];
            $k++;
        }

        return $this->response($tabSousCategorie);
        /*  return $this->json([
            'data' => $tabSousCategorie,

        ], 200); */
    }

    #[Route('/sous_categories/categorie/{code}', name: 'api_sousCategorie_by_categorie_code', methods: ['GET'])]
    public function getAllSousCategorieByCategorie($code, SousCategorieRepository $sousCategorieRepository): Response
    {
        /* try { */

        $sousCategories = $sousCategorieRepository->getSousCategorieByVisite($code);
        $tabSousCategorie = [];
        $k = 0;

        foreach ($sousCategories as $value) {
            $tabSousCategorie[$k]['total'] = $value['_total'] ? (int)$value['_total'] : 0;
            $tabSousCategorie[$k]['id'] = $value['id'];
            $tabSousCategorie[$k]['libelle'] = $value['libelle'];
            $tabSousCategorie[$k]['image'] = [
                'fileNamePath' =>  $value['image']
            ];
            $k++;
        }

        return $this->response($tabSousCategorie);
        /* return $this->json([
            'data' => $tabSousCategorie,

        ], 200); */
    }


    #[Route('/getOne/{id}', name: 'api_sousCategorie_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function getOne(?SousCategorie $sousCategorie)
    {
        /*  $sousCategorie = $sousCategorieRepository->find($id);*/


        try {
            if ($sousCategorie) {
                $response = $this->response($sousCategorie->getCategorie()->getLibelle() . ' | ' . $sousCategorie->getLibelle());
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_sousCategorie_create', methods: ['POST'])]
    /**
     * Permet de créer une sousCategorie.
     *
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function create(Request $request, SousCategorieRepository $sousCategorieRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $sousCategorie = $sousCategorieRepository->findOneBy(array('code' => $data->code));
            if ($sousCategorie == null) {
                $sousCategorie = new SousCategorie();
                $sousCategorie->setCode($data->code);
                $sousCategorie->setLibelle($data->libelle);

                // On sauvegarde en base
                $sousCategorieRepository->add($sousCategorie, true);

                // On retourne la confirmation
                $response = $this->response($sousCategorie);
            } else {
                $this->setMessage("Cette ressource existe deja en base");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', name: 'api_sousCategorie_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une sousCategorie.
     *
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function update(Request $request, SousCategorieRepository $sousCategorieRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $sousCategorie = $sousCategorieRepository->find($id);
            if ($sousCategorie != null) {

                $sousCategorie->setCode($data->code);
                $sousCategorie->setLibelle($data->libelle);

                // On sauvegarde en base
                $sousCategorieRepository->add($sousCategorie, true);

                // On retourne la confirmation
                $response = $this->response($sousCategorie);
            } else {
                $this->setMessage("Cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_sousCategorie_delete', methods: ['POST'])]
    /**
     * permet de supprimer une sousCategorie en offrant un identifiant.
     *
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, SousCategorieRepository $sousCategorieRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $sousCategorie = $sousCategorieRepository->find($id);
            if ($sousCategorie != null) {

                $sousCategorieRepository->remove($sousCategorie, true);

                // On retourne la confirmation
                $response = $this->response($sousCategorie);
            } else {
                $this->setMessage("Cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_sousCategorie_active', methods: ['GET'])]
    /**
     * Permet d'activer une sousCategorie en offrant un identifiant.
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function active(?SousCategorie $sousCategorie, SousCategorieRepository $sousCategorieRepository)
    {
        /*  $sousCategorie = $sousCategorieRepository->find($id);*/
        try {
            if ($sousCategorie) {

                //$sousCategorie->setCode("555"); //TO DO nous ajouter un champs active
                $sousCategorieRepository->add($sousCategorie, true);
                $response = $this->response($sousCategorie);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/active/multiple', name: 'api_sousCategorie_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="SousCategorie")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, SousCategorieRepository $sousCategorieRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeSousCategories = $sousCategorieRepository->findAllByListId($data->ids);
            foreach ($listeSousCategories as $listeSousCategorie) {
                //$listeSousCategorie->setCode("555");  //TO DO nous ajouter un champs active
                $sousCategorieRepository->add($listeSousCategorie, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
