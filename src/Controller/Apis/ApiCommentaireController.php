<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Commentaire;
use App\Entity\ServicePrestataire;
use App\Repository\CommentaireRepository;
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

#[Route('/api/commentaire')]
class ApiCommentaireController extends ApiInterface
{
    #[Route('/', name: 'api_commentaire', methods: ['GET'])]
    /**
     * Affiche toutes les commentaires.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Commentaire::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function getAll(CommentaireRepository $commentaireRepository): Response
    {
        try {

            $commentaires = $commentaireRepository->findAll();
            $response = $this->response($commentaires);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_commentaire_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function getOne(?Commentaire $commentaire)
    {
        /*  $commentaire = $commentaireRepository->find($id);*/
        try {
            if ($commentaire) {
                $response = $this->response($commentaire);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($commentaire);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_commentaire_create', methods: ['POST'])]
    /**
     * Permet de créer une commentaire.
     *
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function create(
        Request $request,
        CommentaireRepository $commentaireRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        UserFrontRepository $userFrontRepository
    ) {
        try {
            $data = json_decode($request->getContent());

            $commentaireData = $commentaireRepository->findOneBy(array('utilisateur' => $data->user, 'service' => $data->service));
            if ($commentaireData == null) {
                $commentaire = new Commentaire();
                $commentaire->setUtilisateur($userFrontRepository->find($data->user));
                $commentaire->setMessage($data->message);
                $commentaire->setService($servicePrestataireRepository->find($data->service));
                $commentaireRepository->save($commentaire, true);

                // On retourne la confirmation
                $response = $this->response($commentaire);
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


    #[Route('/update/{user}/{service}', name: 'api_commentaire_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une commentaire.
     *
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function update(
        Request $request,
        CommentaireRepository $commentaireRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        UserFrontRepository $userFrontRepository,
        $user,
        $service
    ) {
        try {
            $data = json_decode($request->getContent());

            $commentaireData = $commentaireRepository->findOneBy(array('utilisateur' => $user, 'service' => $service));
            if ($commentaireData == null) {
                $commentaire = new Commentaire();
                $commentaire->setUtilisateur($userFrontRepository->find($user));
                $commentaire->setMessage($data->message);
                $commentaire->setService($servicePrestataireRepository->find($service));
                $commentaireRepository->save($commentaire, true);

                // On retourne la confirmation
                $response = $this->response($commentaire);
            } else {

                $commentaireData->setUtilisateur($userFrontRepository->find($user));
                $commentaireData->setMessage($data->message);
                $commentaireData->setService($servicePrestataireRepository->find($service));
                $commentaireRepository->save($commentaire, true);


                // On sauvegarde en base
                $commentaireRepository->save($commentaireData, true);

                // On retourne la confirmation
                $response = $this->response($commentaireData);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_commentaire_delete', methods: ['POST'])]
    /**
     * permet de supprimer une commentaire en offrant un identifiant.
     *
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, CommentaireRepository $commentaireRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $commentaire = $commentaireRepository->find($id);
            if ($commentaire != null) {

                $commentaireRepository->remove($commentaire, true);

                // On retourne la confirmation
                $response = $this->response($commentaire);
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


    #[Route('/active/{id}', name: 'api_commentaire_active', methods: ['GET'])]
    /**
     * Permet d'activer une commentaire en offrant un identifiant.
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function active(?Commentaire $commentaire, CommentaireRepository $commentaireRepository)
    {
        /*  $commentaire = $commentaireRepository->find($id);*/
        try {
            if ($commentaire) {

                //$commentaire->setCode("555"); //TO DO nous ajouter un champs active
                $commentaireRepository->save($commentaire, true);
                $response = $this->response($commentaire);
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


    #[Route('/active/multiple', name: 'api_commentaire_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Commentaire")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, CommentaireRepository $commentaireRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeCommentaires = $commentaireRepository->findAllByListId($data->ids);
            foreach ($listeCommentaires as $listeCommentaire) {
                //$listeCommentaire->setCode("555");  //TO DO nous ajouter un champs active
                $commentaireRepository->add($listeCommentaire, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
