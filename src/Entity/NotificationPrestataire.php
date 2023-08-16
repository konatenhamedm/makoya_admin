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
    private ?Prestataire $prestataire = null;

    #[ORM\ManyToOne(inversedBy: 'notificationPrestataires')]
    private ?Notification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestataire(): ?Prestataire
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Prestataire $prestataire): static
    {
        $this->prestataire = $prestataire;

        return $this;
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
}
