<?php


namespace App\Events;
use App\Entity\UserFront;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurSimple;
use App\Repository\UserFrontRepository;
use App\Repository\UtilisateurRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthenticationSuccessListener
{
    private $utilisateurRepository;
    private $userFrontRepository;
    public function __construct(UtilisateurRepository $utilisateurRepository,UserFrontRepository $userFrontRepository){
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

        if (!$user instanceof UserFront ) {
            return;
        }
       /* if (!$user instanceof Utilisateur ) {
            return;
        }*/

        if($user instanceof UtilisateurSimple ){
            $userData = $this->userFrontRepository->findBy(array('id'=>$user->getId()));
            dd($userData);
           // dd($userData);
    $data['data'] =   $userData;
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