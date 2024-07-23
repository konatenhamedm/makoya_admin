<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\PubliciteEncart;
use App\Repository\PubliciteEncartRepository;
use App\Repository\PubliciteImageRepository;
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

#[Route('/api/publicite')]
class ApiPubliciteController extends ApiInterface
{
    #[Route('/', name: 'api_publicite', methods: ['GET'])]
    /**
     * Affiche toutes les publicites d'ordre 1.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=PubliciteEncart::class, groups={"assurance_read"}))
     *     )
     * )
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function getAll(PubliciteImageRepository $publiciteRepository): Response
    {
        //dd($publicites);
        try {

            $publiciteImages = $publiciteRepository->getPubliciteEncart(1);
            $tabPubliciteEncart = [];
            $i = 0;
            foreach ($publiciteImages as $key => $image) {
                /*  $tabPubliciteEncart[$i]['id'] = $value->getId();
                $tabPubliciteEncart[$i]['libelle'] = $value->getLibelle(); */

                $tabPubliciteEncart[$i] = [
                    'fileNamePath' =>   $image['path'] . '/' . $image['alt'],
                    'libelle' =>  $image['libelle'],
                    'lien' =>  $image['lien'],
                    'description' =>  $image['description'],
                ];





                $i++;
            }
            $response = $this->response($tabPubliciteEncart);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }
    #[Route('/ordre', name: 'api_publicite_ordre_2', methods: ['GET'])]
    /**
     * Affiche toutes les publicites d'ordre 2.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=PubliciteEncart::class, groups={"assurance_read"}))
     *     )
     * )
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function getAllOrdre2(PubliciteImageRepository $publiciteRepository): Response
    {
        try {

            $publiciteImages = $publiciteRepository->getPubliciteEncart(2);
            // dd($publicites);
            $tabPubliciteEncart = [];
            $i = 0;
            foreach ($publiciteImages as $key => $image) {
                /*  $tabPubliciteEncart[$i]['id'] = $value->getId();
                $tabPubliciteEncart[$i]['libelle'] = $value->getLibelle(); */

                $tabPubliciteEncart[$i] = [
                    'fileNamePath' =>   $image['path'] . '/' . $image['alt'],
                    'libelle' =>  $image['libelle'],
                    'lien' =>  $image['lien'],
                    'description' =>  $image['description'],
                ];





                $i++;
            }
            $response = $this->response($tabPubliciteEncart);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }

    #[Route('/categorie/{id}', name: 'api_publicite_categorie', methods: ['GET'])]
    /**
     * Affiche toutes les publicites categorie.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=PubliciteCategorie::class, groups={"assurance_read"}))
     *     )
     * )
     * @OA\Tag(name="PubliciteCategorie")
     * @Security(name="Bearer")
     */
    public function getCategorie(PubliciteImageRepository $publiciteRepository, $id): Response
    {
        try {

            $publiciteImages = $publiciteRepository->getPublicitecategorie($id);
            // dd($publiciteImages);

            $tabPubliciteEncart = [];
            $i = 0;
            foreach ($publiciteImages as $key => $image) {
                /*  $tabPubliciteEncart[$i]['id'] = $value->getId();
                $tabPubliciteEncart[$i]['libelle'] = $value->getLibelle(); */

                $tabPubliciteEncart[$i] = [
                    'fileNamePath' =>   $image['path'] . '/' . $image['alt'],
                    'libelle' =>  $image['libelle'],
                    'lien' =>  $image['lien'],
                    'description' =>  $image['description'],
                ];

                $i++;
            }
            $response = $this->response($tabPubliciteEncart);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_publicite_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function getOne(?PubliciteEncart $publicite)
    {
        /*  $publicite = $publiciteRepository->find($id);*/
        try {
            if ($publicite) {
                $response = $this->response($publicite);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($publicite);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_publicite_create', methods: ['POST'])]
    /**
     * Permet de créer une publicite.
     *
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function create(Request $request, PubliciteEncartRepository $publiciteRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $publicite = $publiciteRepository->findOneBy(array('code' => $data->code));
            if ($publicite == null) {
                $publicite = new PubliciteEncart();
                $publicite->setCode($data->code);
                $publicite->setLibelle($data->libelle);

                // On sauvegarde en base
                $publiciteRepository->add($publicite, true);

                // On retourne la confirmation
                $response = $this->response($publicite);
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


    #[Route('/update/{id}', name: 'api_publicite_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une publicite.
     *
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function update(Request $request, PubliciteEncartRepository $publiciteRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $publicite = $publiciteRepository->find($id);
            if ($publicite != null) {

                $publicite->setCode($data->code);
                $publicite->setLibelle($data->libelle);

                // On sauvegarde en base
                $publiciteRepository->add($publicite, true);

                // On retourne la confirmation
                $response = $this->response($publicite);
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


    #[Route('/delete/{id}', name: 'api_publicite_delete', methods: ['POST'])]
    /**
     * permet de supprimer une publicite en offrant un identifiant.
     *
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, PubliciteEncartRepository $publiciteRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $publicite = $publiciteRepository->find($id);
            if ($publicite != null) {

                $publiciteRepository->remove($publicite, true);

                // On retourne la confirmation
                $response = $this->response($publicite);
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


    #[Route('/active/{id}', name: 'api_publicite_active', methods: ['GET'])]
    /**
     * Permet d'activer une publicite en offrant un identifiant.
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function active(?PubliciteEncart $publicite, PubliciteEncartRepository $publiciteRepository)
    {
        /*  $publicite = $publiciteRepository->find($id);*/
        try {
            if ($publicite) {

                //$publicite->setCode("555"); //TO DO nous ajouter un champs active
                $publiciteRepository->add($publicite, true);
                $response = $this->response($publicite);
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


    #[Route('/active/multiple', name: 'api_publicite_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="PubliciteEncart")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, PubliciteEncartRepository $publiciteRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listePubliciteEncarts = $publiciteRepository->findAllByListId($data->ids);
            foreach ($listePubliciteEncarts as $listePubliciteEncart) {
                //$listePubliciteEncart->setCode("555");  //TO DO nous ajouter un champs active
                $publiciteRepository->add($listePubliciteEncart, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
