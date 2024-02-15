<?php

namespace App\Entity;

use App\Repository\NotificationPrestataireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationPrestataireRepository::class)]
class NotificationPrestataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'notificationPrestataires')]
    private ?Notification $notification = null;

    #[ORM\ManyToOne(inversedBy: 'notificationPrestataires')]
    private ?UserFront $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function sendNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): static
    {
        $this->notification = $notification;

        return $this;
    }

    public function getUtilisateur(): ?UserFront
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?UserFront $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
