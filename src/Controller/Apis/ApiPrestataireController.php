<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Prestataire;
use App\Repository\PrestataireRepository;
use App\Repository\QuartierRepository;
use App\Repository\UserFrontRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\HttpFoundation\empty;
use function Symfony\Component\String\toString;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api/prestataire')]
class ApiPrestataireController extends ApiInterface
{
    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Prestataire::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'PR' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    #[Route('/', name: 'api_prestataire', methods: ['GET'])]
    /**
     * Affiche toutes les civiltes.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Prestataire::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function getAll(PrestataireRepository $prestataireRepository): Response
    {
        try {

            $prestataires = $prestataireRepository->findAll();
            $response = $this->response($prestataires);
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_prestataire_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function getOne(?Prestataire $prestataire)
    {
        /*  $prestataire= $prestataireRepository->find($id);*/
        try {
            if ($prestataire) {
                $response = $this->response($prestataire);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($prestataire);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_prestataire_create', methods: ['POST', 'GET'])]
    /**
     * Permet de créer une prestataire.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function create(Request $request, PrestataireRepository $prestataireRepository, QuartierRepository $quartierRepository)
    {
        try {
            // $data = json_decode($request->getContent());

            // dd($request->get("username"));
            $prestataire = $prestataireRepository->findOneBy(array('email' => $request->get('email')));
            if ($prestataire == null) {
                if (
                    $request->get('contact') == null ||
                    $request->get('denominationSociale') == null ||
                    $request->get('email') == null ||
                    // $request->get('username')  == null ||
                    $request->get('quartier') == null ||
                    // empty($request->files->get('logo')) ||
                    $request->get('password') == null
                ) {
                    $this->setMessage("Il existe certains champs vides !!!");
                    $response = $this->response(null);
                } else {

                    $prestataire = new Prestataire();
                    $prestataire->setContactPrincipal($request->get('contact'));
                    $prestataire->setDenominationSociale($request->get('denominationSociale'));
                    $prestataire->setQuartier($quartierRepository->find($request->get('quartier')));
                    $prestataire->setReference($this->numero());
                    $prestataire->setStatut("Non");

                    $uploadedFile = $request->files->get('logo');

                    //dd($uploadedFile);
                    $names = 'document_' . "01";
                    $filePrefix  = str_slug($names);

                    if ($uploadedFile) {
                        $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);
                        $fichier = $this->utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                        if ($fichier) {
                            $prestataire->setLogo($fichier);
                        }
                    }

                    if ($request->get('situation') == 'checked') {
                        $prestataire->setLongitude($request->get('longitude'));
                        $prestataire->setLattitude($request->get('latitude'));
                    }

                    $prestataire->setEmail($request->get('email'));
                    $prestataire->setDateCreation(new DateTime());
                    $prestataire->setUsername($request->get('denominationSociale'));
                    //$this->hasher->hashPassword($utilisateur, 'admin')
                    $prestataire->setPassword($this->hasher->hashPassword($prestataire, $request->get('password')));


                    // On sauvegarde en base
                    $prestataireRepository->save($prestataire, true);

                    $this->setMessage("Opération éffectuée avec success");
                    $response = $this->response(null);
                    $response->headers->set('Access-Control-Allow-Origin', '*');
                }
            } else {
                $this->setMessage("Cette ressource existe deja en base");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->response(null);
        }


        return $response;
    }
    #[Route('/create_2', name: 'api_prestataire_create_2', methods: ['POST', 'GET'])]
    /**
     * Permet de créer une prestataire.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function create2(Request $request, PrestataireRepository $prestataireRepository, QuartierRepository $quartierRepository, UserFrontRepository $userFrontRepository)
    {

        $data = json_decode($request->getContent());

        // dd($data->username);
        $prestataire = $prestataireRepository->findOneBy(array('email' => $data->email));
        $username = $userFrontRepository->findOneBy(array('username' => $data->denominationSociale));
        if ($prestataire == null && $username == null) {
            if (
                $data->contact == null ||
                $data->denominationSociale == null ||
                $data->email == null ||
                //$data->username  == null ||
                $data->quartier == null ||
                // empty($request->files->get('logo')) ||
                $data->password == null
            ) {
                $this->setMessage("Il existe certains champs vides !!!");
                $response = $this->response(null);
            } else {

                $prestataire = new Prestataire();
                $prestataire->setContactPrincipal($data->contact);
                $prestataire->setDenominationSociale($data->denominationSociale);
                $prestataire->setQuartier($quartierRepository->find($data->quartier));
                $prestataire->setReference($this->numero());
                $prestataire->setStatut("Non");

                //dd($data->logo);

                $uploadedFile = $request->files->get('logo');

                dd($uploadedFile);
                $names = 'document_' . "01";
                $filePrefix  = str_slug($names);

                if ($uploadedFile) {
                    $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);
                    $fichier = $this->utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                    if ($fichier) {

                        $prestataire->setLogo($fichier);
                    }
                }

                if ($data->situation[0] == 'checked') {
                    $prestataire->setLongitude($data->longitude);
                    $prestataire->setLattitude($data->latitude);
                }

                $prestataire->setEmail($data->email);
                $prestataire->setUsername($data->denominationSociale);
                $prestataire->setDateCreation(new DateTime());
                //$this->hasher->hashPassword($utilisateur, 'admin')
                $prestataire->setPassword($this->hasher->hashPassword($prestataire, $data->password));


                // On sauvegarde en base
                $prestataireRepository->save($prestataire, true);

                // On retourne la confirmation
                $this->setMessage("Opération éffectuée avec success");
                $response = $this->response(null);
            }
        } else {
            $this->setMessage("Cette ressource existe deja en base");
            $this->setStatusCode(300);
            $response = $this->response(null);
        }
        /*  } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->responseAdd(null);
        } */


        return $response;
    }


    #[Route('/update/{id}', name: 'api_prestataire_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une prestataire.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function update(Request $request, PrestataireRepository $prestataireRepository, $id, QuartierRepository $quartierRepository)
    {
        try {
            $data = json_decode($request->getContent());
            // dd(empty($request->get('contact')));
            $prestataire = $prestataireRepository->find($id);
            if ($prestataire != null) {


                if ((empty($request->get('contact'))) || (empty($request->get('denominationSociale'))) || (empty($request->get('email'))) || (empty($request->get('username'))) || (empty($request->files->get('logo')))
                    || (empty($request->get('password')))
                    || (empty($request->get('quartier')))
                ) {
                    $this->setMessage("Il existe certains champs vides !!!");
                    $response = $this->response(null);
                } else {


                    $prestataire->setContactPrincipal($request->get('contact'));
                    $prestataire->setDenominationSociale($request->get('denominationSociale'));

                    $uploadedFile = $request->files->get('logo');
                    $names = 'document_' . "01";
                    $filePrefix  = str_slug($names);

                    if ($uploadedFile) {
                        $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);
                        $fichier = $this->utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                        if ($fichier) {

                            $prestataire->setLogo($fichier);
                        }
                    }

                    $prestataire->setEmail($request->get('email'));
                    $prestataire->setQuartier($quartierRepository->find($request->get('quartier')));
                    $prestataire->setUsername($request->get('username'));
                    $prestataire->setPassword($this->hasher->hashPassword($prestataire, $request->get('password')));


                    // On sauvegarde en base
                    $prestataireRepository->save($prestataire, true);

                    // On retourne la confirmation
                    $response = $this->response($prestataire);
                }
            } else {
                $this->setMessage("Cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_prestataire_delete', methods: ['POST'])]
    /**
     * permet de supprimer une prestataireen offrant un identifiant.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, PrestataireRepository $prestataireRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $prestataire = $prestataireRepository->find($id);
            if ($prestataire != null) {

                $prestataireRepository->remove($prestataire, true);

                // On retourne la confirmation
                $response = $this->response($prestataire);
            } else {
                $this->setMessage("Cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_prestataire_active', methods: ['GET'])]
    /**
     * Permet d'activer une prestataireen offrant un identifiant.
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function active(?Prestataire $prestataire, PrestataireRepository $prestataireRepository)
    {
        /*  $prestataire= $prestataireRepository->find($id);*/
        try {
            if ($prestataire) {

                //$prestataire->setCode("555"); //TO DO nous ajouter un champs active
                $prestataireRepository->save($prestataire, true);
                $response = $this->response($prestataire);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/active/multiple', name: 'api_prestataire_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, PrestataireRepository $prestataireRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listePrestataires = $prestataireRepository->findAllByListId($data->ids);
            foreach ($listePrestataires as $listePrestataire) {
                //$listePrestataire->setCode("555");  //TO DO nous ajouter un champs active
                $prestataireRepository->save($listePrestataire, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->response(null);
        }
        return $response;
    }

    #[Route('/reset/password', name: 'api_user_reset_password', methods: ['POST'])]
    /**
     * Permet de reinitialiser le mot de passe.
     *
     * @OA\Tag(name="UserFront")
     * @Security(name="Bearer")
     */
    public function ResetPassword()
    {
        try {
        } catch (\Exception $exception) {
            $this->setMessage($exception);
            $response = $this->response(null);
        }
    }
}
