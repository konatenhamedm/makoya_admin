<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Favorie;
use App\Entity\ServicePrestataire;
use App\Repository\FavorieRepository;
use App\Repository\ServicePrestataireRepository;
use App\Repository\UserFrontRepository;
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

#[Route('/api/favorie')]
class ApiFavorieController extends ApiInterface
{
    #[Route('/', name: 'api_favorie', methods: ['GET'])]
    /**
     * Affiche toutes les favories.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Favorie::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function getAll(FavorieRepository $favorieRepository): Response
    {
        try {

            $favories = $favorieRepository->findAll();
            $response = $this->response($favories);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_favorie_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function getOne(?Favorie $favorie)
    {
        /*  $favorie = $favorieRepository->find($id);*/
        try {
            if ($favorie) {
                $response = $this->response($favorie);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($favorie);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_favorie_create', methods: ['POST'])]
    /**
     * Permet de créer une favorie.
     *
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function create(
        Request $request,
        FavorieRepository $favorieRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        UserFrontRepository $userFrontRepository
    ) {
        try {
            $data = json_decode($request->getContent());

            $favorie = $favorieRepository->findOneBy(array('utilisateur' => $data->user, 'service' => $data->service));
            if ($favorie == null) {
                $favorie = new Favorie();
                $favorie->setUtilisateur($userFrontRepository->find($data->user));
                $favorie->setEtat(true);
                $favorie->setService($servicePrestataireRepository->find($data->service));
                $favorieRepository->save($favorie, true);

                // On retourne la confirmation
                $response = $this->response($favorie);
            } else {
                $favorie->isEtat() ? $favorie->setEtat(false) : $favorie->setEtat(true);
                $favorieRepository->save($favorie, true);
                $response = $this->response($favorie);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{user}/{service}', name: 'api_favorie_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une favorie.
     *
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function update(
        Request $request,
        FavorieRepository $favorieRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        UserFrontRepository $userFrontRepository,
        $user,
        $service
    ) {
        try {
            $data = json_decode($request->getContent());

            $favorieData = $favorieRepository->findOneBy(array('utilisateur' => $user, 'service' => $service));
            if ($favorieData == null) {
                $favorie = new Favorie();
                $favorie->setUtilisateur($userFrontRepository->find($user));
                $favorie->setEtat(true);
                $favorie->setService($servicePrestataireRepository->find($service));
                $favorieRepository->save($favorie, true);

                // On retourne la confirmation
                $response = $this->response($favorie);
            } else {
                $favorieData->isEtat() ? $favorie->setEtat(false) : $favorieData->setEtat(true);
                $favorieRepository->save($favorieData, true);
                $response = $this->response($favorieData);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_favorie_delete', methods: ['POST'])]
    /**
     * permet de supprimer une favorie en offrant un identifiant.
     *
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, FavorieRepository $favorieRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $favorie = $favorieRepository->find($id);
            if ($favorie != null) {

                $favorieRepository->remove($favorie, true);

                // On retourne la confirmation
                $response = $this->response($favorie);
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


    #[Route('/active/{id}', name: 'api_favorie_active', methods: ['GET'])]
    /**
     * Permet d'activer une favorie en offrant un identifiant.
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function active(?Favorie $favorie, FavorieRepository $favorieRepository)
    {
        /*  $favorie = $favorieRepository->find($id);*/
        try {
            if ($favorie) {

                //$favorie->setCode("555"); //TO DO nous ajouter un champs active
                $favorieRepository->save($favorie, true);
                $response = $this->response($favorie);
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


    #[Route('/active/multiple', name: 'api_favorie_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Favorie")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, FavorieRepository $favorieRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeFavories = $favorieRepository->findAllByListId($data->ids);
            foreach ($listeFavories as $listeFavorie) {
                //$listeFavorie->setCode("555");  //TO DO nous ajouter un champs active
                $favorieRepository->add($listeFavorie, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
