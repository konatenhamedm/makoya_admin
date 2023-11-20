<?php


namespace App\Events;

use App\Controller\ApiInterface;
use App\Entity\UserFront;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurSimple;
use App\Repository\UserFrontRepository;
use App\Repository\UtilisateurRepository;
use DateTime;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthenticationSuccessListener extends ApiInterface
{
    private $utilisateurRepository;
    private $userFrontRepository;
    public function __construct(UtilisateurRepository $utilisateurRepository, UserFrontRepository $userFrontRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->userFrontRepository = $userFrontRepository;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        //$response = $this->response($user);
        // dd($data["1"]);
        /*  if (!$user instanceof UserFront || !$user instanceof Utilisateur) {
            return;
        } */

        //dd();
        if ($user instanceof Utilisateur) {
            $userData = $this->utilisateurRepository->find($user->getId());
            //dd($user);

            $data['data'] =   [
                'reference' => $user->getId(),
                'username' => $userData->getUsername(),
            ];
            // dd($data)
            $event->setData($data);
        }

        if ($user instanceof UtilisateurSimple) {
            $userData = $this->userFrontRepository->findOneBy(array('reference' => $user->getReference()));

            //dd($userData["reference"]);$response->getContent();

            $type = str_contains($userData->getReference(), 'PR') ? "prestataire" : "simple";


            $data['user'] =   [
                'id' =>    $userData->getReference(),
                'name' =>    $user->getNomComplet(),
                "type" => $type,
                "email" => $userData->getEmail(),
                'image' => 'http://localhost:8000/uploads/' . $userData->getPhoto()->getPath() . '/' . $userData->getPhoto()->getAlt()
            ];
            $event->setData($data);
        }

        /*if($user instanceof Utilisateur ){
            $data['data'] = array(
                'id'=>$user->getId(),
                'nom'=>$user->getNomComplet(),

            );
            $event->setData($data);

        }*/
    }
}
