<?php


namespace App\Service;

use App\Entity\NotificationPrestataire;
use App\Entity\Notification;
use App\Entity\NotificationUtilisateurSimple;
use App\Entity\Prestataire;
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

    public function getNotification(string $message, string $titre, bool $type = false, Prestataire $prestataire, UtilisateurSimple $utilisateurSimple)
    {


        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setEtat(false);
        $notification->setTitre($titre);
        $notification->setDateCreation(new DateTime());

        $this->em->persist($notification);
        $this->em->flush();

        if ($type) {

            $notificationUtilisateurSimple = new NotificationUtilisateurSimple();
            $notificationUtilisateurSimple->setNotification($notification);
            $notificationUtilisateurSimple->setUtilisateurSimple($utilisateurSimple);
            $this->em->persist($notificationUtilisateurSimple);
            $this->em->flush();
        } else {
            $notificationPrestatire = new NotificationPrestataire();
            $notificationPrestatire->setNotification($notification);
            $notificationPrestatire->setPrestataire($prestataire);
            $this->em->persist($notificationPrestatire);
            $this->em->flush();
        }
    }
}
