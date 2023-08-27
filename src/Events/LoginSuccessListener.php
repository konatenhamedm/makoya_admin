<?php

namespace App\Events;

use App\Entity\User;
use App\Entity\UtilisateurSimple;
use App\Repository\UserFrontRepository;
use App\Repository\UtilisateurRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LoginSuccessListener
{
    private $utilisateurRepository;
    private $userFrontRepository;
    public function __construct(UtilisateurRepository $utilisateurRepository, UserFrontRepository $userFrontRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->userFrontRepository = $userFrontRepository;
    }

    public function onLoginSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();
        $userData = $this->userFrontRepository->findBy(array('reference' => '23US08001'));
        if (!$user instanceof UtilisateurSimple) {
            return;
        }

        //$data['data'] = array(
        // 'roles' => $this->userFrontRepository->find(9)->getReference(),
        //);

        //$event->setData($data);
    }
}
