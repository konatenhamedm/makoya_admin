<?php

namespace App\Entity;

use App\Repository\NotificationUtilisateurSimpleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationUtilisateurSimpleRepository::class)]
class NotificationUtilisateurSimple
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notificationUtilisateurSimples')]
    private ?Notification $notification = null;

    #[ORM\ManyToOne(inversedBy: 'notificationUtilisateurSimples')]
    private ?UtilisateurSimple $utilisateurSimple = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): static
    {
        $this->notification = $notification;

        return $this;
    }

    public function getUtilisateurSimple(): ?UtilisateurSimple
    {
        return $this->utilisateurSimple;
    }

    public function setUtilisateurSimple(?UtilisateurSimple $utilisateurSimple): static
    {
        $this->utilisateurSimple = $utilisateurSimple;

        return $this;
    }
}
