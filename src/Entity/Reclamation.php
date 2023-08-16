<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?UtilisateurSimple $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?PrestataireService $service = null;

    #[ORM\Column]
    private ?bool $accordPrestataire = null;

    #[ORM\Column]
    private ?bool $accordUtilisateurSimple = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateTraitement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?UtilisateurSimple
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?UtilisateurSimple $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

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

    public function getService(): ?PrestataireService
    {
        return $this->service;
    }

    public function setService(?PrestataireService $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function isAccordPrestataire(): ?bool
    {
        return $this->accordPrestataire;
    }

    public function setAccordPrestataire(bool $accordPrestataire): static
    {
        $this->accordPrestataire = $accordPrestataire;

        return $this;
    }

    public function isAccordUtilisateurSimple(): ?bool
    {
        return $this->accordUtilisateurSimple;
    }

    public function setAccordUtilisateurSimple(bool $accordUtilisateurSimple): static
    {
        $this->accordUtilisateurSimple = $accordUtilisateurSimple;

        return $this;
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

    public function getDateTraitement(): ?\DateTimeInterface
    {
        return $this->dateTraitement;
    }

    public function setDateTraitement(\DateTimeInterface $dateTraitement): static
    {
        $this->dateTraitement = $dateTraitement;

        return $this;
    }
}
