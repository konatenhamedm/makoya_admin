<?php


namespace App\Service;

use App\Entity\NotificationPrestataire;
use App\Entity\Notification;
use App\Entity\Prestataire;
use App\Entity\UserFront;
use App\Entity\UtilisateurSimple;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\RouterInterface;

class NotificationService
{

    private $em;
    private $security;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function sendNotification(string $message, string $titre, UserFront $utilisateur)
    {


        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setEtat(false);
        $notification->setTitre($titre);
        $notification->setDateCreation(new DateTime());

        $this->em->persist($notification);
        $this->em->flush();

        $notificationPrestatire = new NotificationPrestataire();
        $notificationPrestatire->setNotification($notification);
        $notificationPrestatire->setUtilisateur($utilisateur);
        $this->em->persist($notificationPrestatire);
        $this->em->flush();
    }
}
