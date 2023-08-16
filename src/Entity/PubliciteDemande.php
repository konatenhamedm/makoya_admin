<?php

namespace App\Entity;

use App\Repository\PubliciteDemandeRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteDemandeRepository::class)]
#[ORM\Table(name: 'publicite_demande_prestataire')]
class PubliciteDemande extends Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteDemandes')]
    private ?Prestataire $prestataire = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datevalidation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $messageRejeter = null;

    /* 
    public function __construct()
    {
        $this->dateCreation = new DateTime();
    } */
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


    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }


    public function getDatevalidation(): ?\DateTimeInterface
    {
        return $this->datevalidation;
    }

    public function setDatevalidation(\DateTimeInterface $datevalidation): static
    {
        $this->datevalidation = $datevalidation;

        return $this;
    }

    public function getMessageRejeter(): ?string
    {
        return $this->messageRejeter;
    }

    public function setMessageRejeter(string $messageRejeter): static
    {
        $this->messageRejeter = $messageRejeter;

        return $this;
    }
}
