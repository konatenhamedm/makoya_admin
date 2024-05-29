<?php


namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\PubliciteDemande;
use App\Repository\PubliciteDemandeRepository;
use App\Repository\PubliciteImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/publicite/user')]
class ApiPubliciteUser extends ApiInterface
{
    #[Route('/', name: 'api_publicte_user', methods: ['GET'])]
    /**
     * Affiche toutes les demande publicité.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=PubliciteDemande::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="PubliciteDemande")
     * @Security(name="Bearer")
     */
    public function getAll(PubliciteDemandeRepository $publiciteDemandeRepository, PubliciteImageRepository $publiciteRepository): Response
    {
        ///dd('');
        try {

            $publiciteImages = $publiciteRepository->getPubliciteUsers();

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
        return $response;;
    }
}
