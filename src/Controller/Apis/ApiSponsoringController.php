<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Sponsoring;
use App\Repository\SponsoringRepository;
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

#[Route('/api/sponsoring')]
class ApiSponsoringController extends ApiInterface
{
    #[Route('/', name: 'api_sponsoring', methods: ['GET'])]
    /**
     * Affiche toutes les sponsorings.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Sponsoring::class, groups={"assurance_read"}))
     *     )
     * )
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function getAll(SponsoringRepository $sponsoringRepository): Response
    {
        try {

            $sponsorings = $sponsoringRepository->getSponsoring();
            // dd($sponsorings);
            $tabSponsoring = [];
            $i = 0;
            foreach ($sponsorings as $key => $value) {
                $tabSponsoring[$i]['id'] = $value->getId();
                $tabSponsoring[$i]['entreprise'] = $value->getEntreprise();
                $tabSponsoring[$i]['dateDebut'] = $value->getDateDebut();
                $tabSponsoring[$i]['dateFin'] = $value->getDateFin();
                $tabSponsoring[$i]['lien'] = $value->getLien();
                $tabSponsoring[$i]['titre'] = $value->getTitre();
                $tabSponsoring[$i]['description'] = $value->getDescription();
                $tabSponsoring[$i]['situation'] = $value->getQuartier()->getCommune()->getNom() . ' - ' . $value->getQuartier()->getNom();
                $tabSponsoring[$i]['image'] = [
                    'fileNamePath' =>  $value->getImage()->getPath() . '/' . $value->getImage()->getAlt()
                ];

                $i++;
            }
            $response = $this->response($tabSponsoring);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_sponsoring_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function getOne(?Sponsoring $sponsoring)
    {
        /*  $sponsoring = $sponsoringRepository->find($id);*/
        try {
            if ($sponsoring) {
                $response = $this->response($sponsoring);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($sponsoring);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_sponsoring_create', methods: ['POST'])]
    /**
     * Permet de créer une sponsoring.
     *
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function create(Request $request, SponsoringRepository $sponsoringRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $sponsoring = $sponsoringRepository->findOneBy(array('code' => $data->code));
            if ($sponsoring == null) {
                $sponsoring = new Sponsoring();
                $sponsoring->setCode($data->code);
                $sponsoring->setLibelle($data->libelle);

                // On sauvegarde en base
                $sponsoringRepository->add($sponsoring, true);

                // On retourne la confirmation
                $response = $this->response($sponsoring);
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


    #[Route('/update/{id}', name: 'api_sponsoring_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une sponsoring.
     *
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function update(Request $request, SponsoringRepository $sponsoringRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $sponsoring = $sponsoringRepository->find($id);
            if ($sponsoring != null) {

                $sponsoring->setCode($data->code);
                $sponsoring->setLibelle($data->libelle);

                // On sauvegarde en base
                $sponsoringRepository->add($sponsoring, true);

                // On retourne la confirmation
                $response = $this->response($sponsoring);
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


    #[Route('/delete/{id}', name: 'api_sponsoring_delete', methods: ['POST'])]
    /**
     * permet de supprimer une sponsoring en offrant un identifiant.
     *
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, SponsoringRepository $sponsoringRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $sponsoring = $sponsoringRepository->find($id);
            if ($sponsoring != null) {

                $sponsoringRepository->remove($sponsoring, true);

                // On retourne la confirmation
                $response = $this->response($sponsoring);
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


    #[Route('/active/{id}', name: 'api_sponsoring_active', methods: ['GET'])]
    /**
     * Permet d'activer une sponsoring en offrant un identifiant.
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function active(?Sponsoring $sponsoring, SponsoringRepository $sponsoringRepository)
    {
        /*  $sponsoring = $sponsoringRepository->find($id);*/
        try {
            if ($sponsoring) {

                //$sponsoring->setCode("555"); //TO DO nous ajouter un champs active
                $sponsoringRepository->add($sponsoring, true);
                $response = $this->response($sponsoring);
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


    #[Route('/active/multiple', name: 'api_sponsoring_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Sponsoring")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, SponsoringRepository $sponsoringRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeSponsorings = $sponsoringRepository->findAllByListId($data->ids);
            foreach ($listeSponsorings as $listeSponsoring) {
                //$listeSponsoring->setCode("555");  //TO DO nous ajouter un champs active
                $sponsoringRepository->add($listeSponsoring, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
