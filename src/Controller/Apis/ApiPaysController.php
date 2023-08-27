<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Pays;
use App\Repository\PaysRepository;
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

#[Route('/api/pays')]
class ApiPaysController extends ApiInterface
{
    #[Route('/', name: 'api_pays', methods: ['GET'])]
    /**
     * Affiche toutes les civiltes.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Pays::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function getAll(PaysRepository $paysRepository): Response
    {
        try {

            $payss = $paysRepository->findAll();
            $response = $this->response($payss);
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_pays_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function getOne(?Pays $pays)
    {
        /*  $pays = $paysRepository->find($id);*/
        try {
            if ($pays) {
                $response = $this->response($pays);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($pays);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_pays_create', methods: ['POST'])]
    /**
     * Permet de créer une pays.
     *
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function create(Request $request, PaysRepository $paysRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $pays = $paysRepository->findOneBy(array('code' => $data->code));
            if ($pays == null) {
                $pays = new Pays();
                $pays->setCode($data->code);
                $pays->setLibelle($data->libelle);

                // On sauvegarde en base
                $paysRepository->add($pays, true);

                // On retourne la confirmation
                $response = $this->response($pays);
            } else {
                $this->setMessage("cette ressource existe deja en base");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', name: 'api_pays_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une pays.
     *
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function update(Request $request, PaysRepository $paysRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $pays = $paysRepository->find($id);
            if ($pays != null) {

                $pays->setCode($data->code);
                $pays->setLibelle($data->libelle);

                // On sauvegarde en base
                $paysRepository->add($pays, true);

                // On retourne la confirmation
                $response = $this->response($pays);
            } else {
                $this->setMessage("cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_pays_delete', methods: ['POST'])]
    /**
     * permet de supprimer une pays en offrant un identifiant.
     *
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, PaysRepository $paysRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $pays = $paysRepository->find($id);
            if ($pays != null) {

                $paysRepository->remove($pays, true);

                // On retourne la confirmation
                $response = $this->response($pays);
            } else {
                $this->setMessage("cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_pays_active', methods: ['GET'])]
    /**
     * Permet d'activer une pays en offrant un identifiant.
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function active(?Pays $pays, PaysRepository $paysRepository)
    {
        /*  $pays = $paysRepository->find($id);*/
        try {
            if ($pays) {

                //$pays->setCode("555"); //TO DO nous ajouter un champs active
                $paysRepository->add($pays, true);
                $response = $this->response($pays);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/active/multiple', name: 'api_pays_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Pays")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, PaysRepository $paysRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listePayss = $paysRepository->findAllByListId($data->ids);
            foreach ($listePayss as $listePays) {
                //$listePays->setCode("555");  //TO DO nous ajouter un champs active
                $paysRepository->add($listePays, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage($exception . toString());
            $response = $this->response(null);
        }
        return $response;
    }
}
