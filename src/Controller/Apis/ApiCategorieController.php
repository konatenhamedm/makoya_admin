<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
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

#[Route('/api/categorie')]
class ApiCategorieController extends ApiInterface
{
    #[Route('/', name: 'api_categorie', methods: ['GET'])]
    /**
     * Affiche toutes les civiltes.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Categorie::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function getAll(CategorieRepository $categorieRepository): Response
    {
        try {

            $categories = $categorieRepository->findAll();
            $response = $this->response($categories);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_categorie_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function getOne(?Categorie $categorie)
    {
        /*  $categorie = $categorieRepository->find($id);*/
        try {
            if ($categorie) {
                $response = $this->response($categorie);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($categorie);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_categorie_create', methods: ['POST'])]
    /**
     * Permet de créer une categorie.
     *
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function create(Request $request, CategorieRepository $categorieRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $categorie = $categorieRepository->findOneBy(array('code' => $data->code));
            if ($categorie == null) {
                $categorie = new Categorie();
                $categorie->setCode($data->code);
                $categorie->setLibelle($data->libelle);

                // On sauvegarde en base
                $categorieRepository->add($categorie, true);

                // On retourne la confirmation
                $response = $this->response($categorie);
            } else {
                $this->setMessage("cette ressource existe deja en base");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', name: 'api_categorie_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une categorie.
     *
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function update(Request $request, CategorieRepository $categorieRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $categorie = $categorieRepository->find($id);
            if ($categorie != null) {

                $categorie->setCode($data->code);
                $categorie->setLibelle($data->libelle);

                // On sauvegarde en base
                $categorieRepository->add($categorie, true);

                // On retourne la confirmation
                $response = $this->response($categorie);
            } else {
                $this->setMessage("cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_categorie_delete', methods: ['POST'])]
    /**
     * permet de supprimer une categorie en offrant un identifiant.
     *
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, CategorieRepository $categorieRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $categorie = $categorieRepository->find($id);
            if ($categorie != null) {

                $categorieRepository->remove($categorie, true);

                // On retourne la confirmation
                $response = $this->response($categorie);
            } else {
                $this->setMessage("cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_categorie_active', methods: ['GET'])]
    /**
     * Permet d'activer une categorie en offrant un identifiant.
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function active(?Categorie $categorie, CategorieRepository $categorieRepository)
    {
        /*  $categorie = $categorieRepository->find($id);*/
        try {
            if ($categorie) {

                //$categorie->setCode("555"); //TO DO nous ajouter un champs active
                $categorieRepository->add($categorie, true);
                $response = $this->response($categorie);
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


    #[Route('/active/multiple', name: 'api_categorie_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, CategorieRepository $categorieRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeCategories = $categorieRepository->findAllByListId($data->ids);
            foreach ($listeCategories as $listeCategorie) {
                //$listeCategorie->setCode("555");  //TO DO nous ajouter un champs active
                $categorieRepository->add($listeCategorie, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
