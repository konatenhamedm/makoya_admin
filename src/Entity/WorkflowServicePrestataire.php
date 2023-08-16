<?php

namespace App\Entity;

use App\Repository\WorkflowServicePrestataireRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: WorkflowServicePrestataireRepository::class)]
class WorkflowServicePrestataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workflowServicePrestataires')]
    #[Assert\NotBlank(message: 'Veuillez renseigner une categorie')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'workflowServicePrestataires')]
    #[Assert\NotBlank(message: 'Veuillez renseigner un service')]
    private ?ServicePrestataire $service = null;

    #[ORM\ManyToOne(inversedBy: 'workflowServicePrestataires')]
    private ?SousCategorie $sousCategorie = null;

    #[ORM\ManyToOne(inversedBy: 'workflowServicePrestataires')]
    #[Assert\NotBlank(message: 'Veuillez renseigner un prestataire')]
    private ?Prestataire $prestataire = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $messageRejeter = null;

    public function __construct()
    {
        $this->datetCreation = new DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getService(): ?ServicePrestataire
    {
        return $this->service;
    }

    public function setService(?ServicePrestataire $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): static
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
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

    public function getDatetCreation(): ?\DateTimeInterface
    {
        return $this->datetCreation;
    }

    public function setDatetCreation(\DateTimeInterface $datetCreation): static
    {
        $this->datetCreation = $datetCreation;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(\DateTimeInterface $dateValidation): static
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getImage(): ?Fichier
    {
        return $this->image;
    }

    public function setImage(?Fichier $image): static
    {
        $this->image = $image;

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
