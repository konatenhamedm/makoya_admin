<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;



    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: NotificationPrestataire::class)]
    private Collection $notificationPrestataires;





    public function __construct()
    {
        $this->notificationPrestataires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }


    /**
     * @return Collection<int, NotificationPrestataire>
     */
    public function sendNotificationPrestataires(): Collection
    {
        return $this->notificationPrestataires;
    }

    public function addNotificationPrestataire(NotificationPrestataire $notificationPrestataire): static
    {
        if (!$this->notificationPrestataires->contains($notificationPrestataire)) {
            $this->notificationPrestataires->add($notificationPrestataire);
            $notificationPrestataire->setNotification($this);
        }

        return $this;
    }

    public function removeNotificationPrestataire(NotificationPrestataire $notificationPrestataire): static
    {
        if ($this->notificationPrestataires->removeElement($notificationPrestataire)) {
            // set the owning side to null (unless already changed)
            if ($notificationPrestataire->sendNotification() === $this) {
                $notificationPrestataire->setNotification(null);
            }
        }

        return $this;
    }
}
