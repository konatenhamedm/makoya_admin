<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Prestataire;
use App\Repository\PrestataireRepository;
use App\Repository\QuartierRepository;
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
        try{

            $prestataires = $prestataireRepository->findAll();
            $response = $this->response($prestataires);

        }catch (\Exception $exception){
            $this->setMessage($exception.toString());
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
        try{
            if($prestataire){
                $response = $this->response($prestataire);
            }else{
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($prestataire);
            }
        }catch (\Exception $exception){
            $this->setMessage($exception.toString());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_prestataire_create', methods: ['POST'])]
    /**
     * Permet de créer une prestataire.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function create(Request $request,PrestataireRepository $prestataireRepository,QuartierRepository $quartierRepository)
    {
            try{
                $data = json_decode($request->getContent());

//dd($request->get(username));
                $prestataire= $prestataireRepository->findOneBy(array('username'=>$request->get('username')));
                if($prestataire== null){
                    if(
                    empty($request->get('contact')) ||
                    empty($request->get('denominationSociale')) ||
                    empty($request->get('email')) ||
                    empty($request->get('username')) ||
                    empty($request->get('quartier')) ||
                    empty($request->files->get('logo')) ||
                    empty($request->get('password'))
                    ){
                        $this->setMessage("Il existe certains champs vides !!!");
                        $response = $this->response(null);

                    }else{

                        $prestataire= new Prestataire();
                        $prestataire->setContactPrincipal($request->get('contact'));
                        $prestataire->setDenominationSociale($request->get('denominationSociale'));
                        $prestataire->setQuartier($quartierRepository->find($request->get('quartier')));

                        $uploadedFile = $request->files->get('logo');
                        $names = 'document_' . "01";
                        $filePrefix  = str_slug($names);

                        if ($uploadedFile) {
                            $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);
                            $fichier = $this->utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                            if ($fichier)
                            {

                                $prestataire->setLogo($fichier);
                            }

                        }

                        $prestataire->setEmail($request->get('email'));
                        $prestataire->setUsername($request->get('username'));
                        $prestataire->setPassword($this->hasher->hashPassword($prestataire, $request->get('password')));


                        // On sauvegarde en base
                        $prestataireRepository->save($prestataire,true);

                        // On retourne la confirmation
                        $response = $this->response($prestataire);
                    }


                }else{
                    $this->setMessage("cette ressource existe deja en base");
                    $this->setStatusCode(300);
                    $response = $this->response(null);

                }
            }catch (\Exception $exception){
                $this->setMessage($exception);
                $response = $this->response(null);
            }


        return $response;
        }


    #[Route('/update/{id}', name: 'api_prestataire_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une prestataire.
     *
     * @OA\Tag(name="Prestataire")
     * @Security(name="Bearer")
     */
    public function update(Request $request,PrestataireRepository $prestataireRepository,$id,QuartierRepository $quartierRepository)
    {
       try{
           $data = json_decode($request->getContent());
          // dd(empty($request->get('contact')));
           $prestataire= $prestataireRepository->find($id);
           if($prestataire!= null){


               if((empty($request->get('contact'))) || (empty($request->get('denominationSociale'))) ||(empty($request->get('email'))) || (empty($request->get('username'))) || ( empty($request->files->get('logo'))) 
               || ( empty($request->get('password')))
               || ( empty($request->get('quartier')))
               ){
                   $this->setMessage("Il existe certains champs vides !!!");
                   $response = $this->response(null);
               }else{


                   $prestataire->setContactPrincipal($request->get('contact'));
                   $prestataire->setDenominationSociale($request->get('denominationSociale'));

                   $uploadedFile = $request->files->get('logo');
                   $names = 'document_' . "01";
                   $filePrefix  = str_slug($names);

                   if ($uploadedFile) {
                       $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);
                       $fichier = $this->utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                       if ($fichier)
                       {

                           $prestataire->setLogo($fichier);
                       }

                   }

                   $prestataire->setEmail($request->get('email'));
                   $prestataire->setQuartier($quartierRepository->find($request->get('quartier')));
                   $prestataire->setUsername($request->get('username'));
                   $prestataire->setPassword($this->hasher->hashPassword($prestataire, $request->get('password')));


                   // On sauvegarde en base
                   $prestataireRepository->save($prestataire,true);

                   // On retourne la confirmation
                   $response = $this->response($prestataire);
               }


           }else{
               $this->setMessage("cette ressource est inexsitante");
               $this->setStatusCode(300);
               $response = $this->response(null);

           }


       }catch (\Exception $exception){
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
    public function delete(Request $request,PrestataireRepository $prestataireRepository,$id)
    {
        try{
            $data = json_decode($request->getContent());

            $prestataire= $prestataireRepository->find($id);
            if($prestataire!= null){

                $prestataireRepository->remove($prestataire,true);

                // On retourne la confirmation
                $response = $this->response($prestataire);

            }else{
                $this->setMessage("cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);

            }
        }catch (\Exception $exception){
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
    public function active(?Prestataire $prestataire,PrestataireRepository $prestataireRepository)
    {
        /*  $prestataire= $prestataireRepository->find($id);*/
        try{
            if($prestataire){

                //$prestataire->setCode("555"); //TO DO nous ajouter un champs active
                $prestataireRepository->save($prestataire,true);
                $response = $this->response($prestataire);
            }else{
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        }catch (\Exception $exception){
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
    public function multipleActive(Request $request,PrestataireRepository $prestataireRepository){
        try{
            $data = json_decode($request->getContent());

            $listePrestataires = $prestataireRepository->findAllByListId($data->ids);
            foreach ($listePrestataires as $listePrestataire) {
                //$listePrestataire->setCode("555");  //TO DO nous ajouter un champs active
                $prestataireRepository->save($listePrestataire,true);
            }
            
            $response = $this->response(null);
        }catch (\Exception $exception){
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
    public function ResetPassword(){
        try {

        }catch (\Exception $exception){
            $this->setMessage($exception);
            $response = $this->response(null);
        }
    }
}
