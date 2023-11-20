<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Quartier;
use App\Repository\CiviliteRepository;
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

#[Route('/api/quartier')]
class ApiQuartierController extends ApiInterface
{
    #[Route('/', name: 'api_quartier', methods: ['GET'])]
    /**
     * Affiche toutes les civiltes.
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
    public function getAll(QuartierRepository $quartierRepository): Response
    {
        try {

            $quartiers = $quartierRepository->findAll();
            $response = $this->response($quartiers);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }




    #[Route('/getOne/{id}', name: 'api_quartier_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function getOne(?Quartier $quartier)
    {
        /*  $quartier = $quartierRepository->find($id);*/
        try {
            if ($quartier) {
                $response = $this->response($quartier);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($quartier);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_quartier_create', methods: ['POST'])]
    /**
     * Permet de créer une quartier.
     *
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function create(Request $request, QuartierRepository $quartierRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $quartier = $quartierRepository->findOneBy(array('code' => $data->code));
            if ($quartier == null) {
                $quartier = new Quartier();
                $quartier->setCode($data->code);
                $quartier->setNom($data->libelle);

                // On sauvegarde en base
                $quartierRepository->add($quartier, true);

                // On retourne la confirmation
                $response = $this->response($quartier);
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


    #[Route('/update/{id}', name: 'api_quartier_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une quartier.
     *
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function update(Request $request, QuartierRepository $quartierRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $quartier = $quartierRepository->find($id);
            if ($quartier != null) {

                $quartier->setCode($data->code);
                $quartier->setNom($data->libelle);

                // On sauvegarde en base
                $quartierRepository->add($quartier, true);

                // On retourne la confirmation
                $response = $this->response($quartier);
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


    #[Route('/delete/{id}', name: 'api_quartier_delete', methods: ['POST'])]
    /**
     * permet de supprimer une quartier en offrant un identifiant.
     *
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, QuartierRepository $quartierRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $quartier = $quartierRepository->find($id);
            if ($quartier != null) {

                $quartierRepository->remove($quartier, true);

                // On retourne la confirmation
                $response = $this->response($quartier);
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


    #[Route('/active/{id}', name: 'api_quartier_active', methods: ['GET'])]
    /**
     * Permet d'activer une quartier en offrant un identifiant.
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function active(?Quartier $quartier, QuartierRepository $quartierRepository)
    {
        /*  $quartier = $quartierRepository->find($id);*/
        try {
            if ($quartier) {

                //$quartier->setCode("555"); //TO DO nous ajouter un champs active
                $quartierRepository->add($quartier, true);
                $response = $this->response($quartier);
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


    #[Route('/active/multiple', name: 'api_quartier_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Quartier")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, QuartierRepository $quartierRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeQuartiers = $quartierRepository->findAllByListId($data->ids);
            foreach ($listeQuartiers as $listeQuartier) {
                //$listeQuartier->setCode("555");  //TO DO nous ajouter un champs active
                $quartierRepository->add($listeQuartier, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
