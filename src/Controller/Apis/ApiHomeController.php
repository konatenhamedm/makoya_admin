<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\NombreClick;
use App\Entity\Pays;
use App\Repository\CategorieRepository;
use App\Repository\NombreClickRepository;
use App\Repository\PaysRepository;
use App\Repository\PrestataireServiceRepository;
use App\Repository\ServiceRepository;
use App\Repository\SousCategorieRepository;
use App\Repository\UserFrontRepository;
use DateTime;
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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage as NA;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

#[Route('/api/home')]
class ApiHomeController extends ApiInterface
{


    #[Route('/{id}/{type}/{user}', name: 'api_home', methods: ['GET'])]
    /**
     * Permet .
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
    public function index(
        Request $request,
        UserFrontRepository $userFrontRepository,
        NombreClickRepository $nombreClickRepository,
        PrestataireServiceRepository $serviceRepository,
        CategorieRepository $categorieRepository,
        SousCategorieRepository $sousCategorieRepository,
        SessionInterface $session,
        $id,
        $type,
        $user
    ) {
        $session->start();
        // $session->migrate(false, 3600);
        //dd($session->getId());
        /*  $sessionStorage = new NativeSessionStorage([]);
        $session = new Session($sessionStorage);
        //$res = exec("ping -s 1 " . "127.0.0.1", $output, $status);
        $sessionStorage->setOptions(['cookie_lifetime' => 1234]);
 */
        // $sessionId = $request->getSession()->getId();
        // $sessionId = $session->getId();

        $sessionId = $session->get('sessionId', []);
        /* unset($sessionId["10-service"]); */
        //dd($sessionId);

        if (empty($sessionId[$id . $type])) {
            $sessionId[$id . $type] = $id . $type . $session->getId();
        }

        $sessionId[$id . $type] == $id . $type . $session->getId();

        $session->set('sessionId', $sessionId);



        //Je recupere par type afin de savoir
        // dd($sessionId);
        if ($userFrontRepository->find($user)) {

            $dataNombreClick = $nombreClickRepository->getData($id . $type . $session->getId(), $type, $id, $userFrontRepository->find($user)->getQuartier()->getId());

            if ($dataNombreClick != null) {
                // dd('pp1');
                $nombreHeure = intval($dataNombreClick->getDateModification()->diff(new DateTime())->format('%h'));

                if ($nombreHeure >= 1 && in_array($dataNombreClick->getMac(), $sessionId)) {
                    $dataNombreClick->setNombre($dataNombreClick->getNombre() + 1);
                    $dataNombreClick->setDateModification(new DateTime());
                    $nombreClickRepository->save($dataNombreClick, true);
                }
            } else {
                $newClick = new NombreClick();
                if ($type == 'categorie') {
                    $newClick->setCategorie($categorieRepository->find($id));
                    $newClick->setType('categorie');
                } elseif ($type == 'service') {
                    $newClick->setService($serviceRepository->find($id));
                    $newClick->setType('service');
                } else {
                    $newClick->setSousCategorie($sousCategorieRepository->find($id));
                    $newClick->setType('sousCategorie');
                }

                $newClick->setQuartier($userFrontRepository->find($user)->getQuartier());
                $newClick->setNombre(1);
                $newClick->setMac($id . $type . $session->getId());
                //dd($newClick);
                // $newClick->setDateModification(new DateTime());
                $nombreClickRepository->save($newClick, true);
            }
        } else {

            $dataNombreClick = $nombreClickRepository->getDataWithoutQuartier($sessionId, $type, $id);
            if ($dataNombreClick != null) {

                $nombreHeure = intval($dataNombreClick->getDateModification()->diff(new DateTime())->format('%h'));

                if ($nombreHeure >= 1 && in_array($dataNombreClick->getMac(), $sessionId)) {
                    $dataNombreClick->setNombre($dataNombreClick->getNombre() + 1);
                    $dataNombreClick->setDateModification(new DateTime());
                    $nombreClickRepository->save($dataNombreClick, true);
                }
            } else {
                $newClick = new NombreClick();

                if ($type == 'categorie') {
                    $newClick->setCategorie($categorieRepository->find($id));
                } elseif ($type == 'service') {
                    $newClick->setService($serviceRepository->find($id));
                } else {
                    $newClick->setSousCategorie($sousCategorieRepository->find($id));
                }

                // $newClick->setQuartier($userFrontRepository->find($user)->getQuartier());
                $newClick->setNombre(1);
                $newClick->setMac($id . $type . $session->getId());
                $newClick->setDateModification(new DateTime());
                $nombreClickRepository->save($newClick, true);
            }
        }

        $response = $this->response('ok');
        return  $response;
    }



    #[Route('/create', name: 'api_nombre_click', methods: ['POST', 'GET'])]
    /**
     * Permet de créer une categorie.
     *
     * @OA\Tag(name="Categorie")
     * @Security(name="Bearer")
     */
    public function create(
        Request $request,
        UserFrontRepository $userFrontRepository,
        NombreClickRepository $nombreClickRepository,
        ServiceRepository $serviceRepository,
        CategorieRepository $categorieRepository,
        SousCategorieRepository $sousCategorieRepository,
        SessionInterface $session
    ) {
        $session->start();

        $session->migrate(false, 36000);

        $sessionId = $session->getId();


        //$session = $this->requestStack->getSession();

        $cookie = Cookie::create('ddd' . $sessionId, $sessionId,  time() + 36000);

        setcookie('user_id', 10, 0, '/', 'orion.dev');
        /*  try { */
        $data = json_decode($request->getContent());
        $dateC = $cookie->getName($cookie);
        //dd();
        if ($userFrontRepository->find($data->user)) {

            $dataNombreClick = $nombreClickRepository->getData($sessionId, $data->type, $data->id, $userFrontRepository->find($data->user)->getQuartier()->getId());

            if ($dataNombreClick != null) {

                $nombreHeure = intval($dataNombreClick->getDateModification()->diff(new DateTime())->format('%h'));

                /*   if ($nombreHeure >= 1) { */
                $dataNombreClick->setNombre($dataNombreClick->getNombre() + 1);
                $dataNombreClick->setDateModification(new DateTime());
                $nombreClickRepository->save($dataNombreClick, true);
                /*  } */
            } else {
                $newClick = new NombreClick();
                if ($data->type == 'categorie') {
                    $newClick->setCategorie($categorieRepository->find($data->id));
                    $newClick->setType('categorie');
                } elseif ($data->type == 'service') {
                    $newClick->setService($serviceRepository->find($data->id));
                    $newClick->setType('service');
                } else {
                    $newClick->setSousCategorie($sousCategorieRepository->find($data->id));
                    $newClick->setType('sousCategorie');
                }

                $newClick->setQuartier($userFrontRepository->find($data->user)->getQuartier());
                $newClick->setNombre(1);
                $newClick->setMac($sessionId);
                //dd($newClick);
                // $newClick->setDateModification(new DateTime());
                $nombreClickRepository->save($newClick, true);
            }
        } else {

            $dataNombreClick = $nombreClickRepository->getDataWithoutQuartier($sessionId, $data->type, $data->id);
            if ($dataNombreClick != null) {

                $nombreHeure = intval($dataNombreClick->getDateModification()->diff(new DateTime())->format('%h'));

                if ($nombreHeure >= 1) {
                    $dataNombreClick->setNombre($dataNombreClick->getNombre() + 1);
                    $dataNombreClick->setDateModification(new DateTime());
                    $nombreClickRepository->save($dataNombreClick, true);
                }
            } else {
                $newClick = new NombreClick();

                if ($data->type == 'categorie') {
                    $newClick->setCategorie($categorieRepository->find($data->id));
                } elseif ($data->type == 'service') {
                    $newClick->setService($serviceRepository->find($data->id));
                } else {
                    $newClick->setSousCategorie($sousCategorieRepository->find($data->id));
                }

                $newClick->setQuartier($userFrontRepository->find($data->user)->getQuartier());
                $newClick->setNombre(1);
                $newClick->setMac($sessionId);
                $newClick->setDateModification(new DateTime());
                $nombreClickRepository->save($newClick, true);
            }
        }

        dd($sessionId);
    }
}
