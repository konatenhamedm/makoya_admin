<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Fichier;
use App\Entity\UserFront;
use App\Repository\FichierRepository;
use App\Repository\PrestataireRepository;
use App\Repository\UserFrontRepository;
use App\Repository\UtilisateurSimpleRepository;
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

#[Route('/api/front')]
class ApiUseFrontController extends ApiInterface
{

    #[Route('/getOne/{email}', name: 'api_front_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="UserFront")
     * @Security(name="Bearer")
     */
    public function getOne(?UserFront $front, $email, PrestataireRepository $prestataireRepository, UtilisateurSimpleRepository $utilisateurSimpleRepository, FichierRepository $fichierRepository)
    {
        //  $fronts = $frontRepository->findOneBy(array('reference' => $reference));


        // try {

        /*   if ($front) {
                $response = $this->response($front);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($front);
            } */
        //  } catch (\Exception $exception) {
        //  $this->setMessage($exception->getMessage());
        //  $response = $this->response(null);
        //}
        $image = null;

        if (str_contains($front->getReference(), 'PR')) {
            $type = "Prestataire";
            $prestataire = $prestataireRepository->findOneBy(array('reference' => $front->getReference()));

            $nom = $prestataire->getDenominationSociale();
            $image = 'http://localhost:8000/' . $prestataire->getLogo()->getFileNamePath();
        } else {
            $type = "Simple";
            $utilisateur = $utilisateurSimpleRepository->findOneBy(array('reference' => $front->getReference()));
            $nom = $utilisateur->getNomComplet();
            $image = 'http://localhost:8000/' . $utilisateur->getPhoto()->getFileNamePath();
        }
        //   = str_contains($front->getReference(), 'PR') ? "prestataire" : "simple";
        $response = [
            "username" => $front->getUsername(),
            "email" => $front->getEmail(),
            "reference" => $front->getReference(),
            "type" => $type,
            "nomComplet" => $nom,
            "image" => $image,
        ];


        return $this->json([
            'data' => $response
        ], 200);
    }
}
