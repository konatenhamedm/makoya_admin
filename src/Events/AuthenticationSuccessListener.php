<?php


namespace App\Events;

use App\Controller\ApiInterface;
use App\Entity\UserFront;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurSimple;
use App\Repository\UserFrontRepository;
use App\Repository\UtilisateurRepository;
use DateTime;
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
        $response = $this->response($user);
        // dd($data["1"]);
        /*   if (!$user instanceof UserFront) {
            return;
        } */
        /* if (!$user instanceof Utilisateur ) {
            return;
        }*/

        if ($user instanceof UtilisateurSimple) {
            $userData = $this->userFrontRepository->findOneBy(array('reference' => '23US08001'));
            //dd($user);
            //dd($userData["reference"]);$response->getContent();
            $data['data'] =   [
                'reference' => $this->userFrontRepository->find(9)->getReference(),
                'username' => $this->userFrontRepository->find(9)->getUsername(),

                "avatar" => "https://fr.web.img6.acsta.net/newsv7/21/02/26/16/13/3979241.jpg",
                "id" => $this->userFrontRepository->find(9)->getUserIdentifier(),
                "accessToken" => "ffff",
                "expiredAt" => new DateTime(),

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
