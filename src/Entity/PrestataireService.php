<?php

namespace App\Entity;

use App\Repository\PrestataireServiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestataireServiceRepository::class)]
#[ORM\Table(name:'gestion_prestataire_service')]
class PrestataireService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    private ?Prestataire $prestataire = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    private ?ServicePrestataire $service = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    private ?SousCategorie $sousCategorie = null;

  

    #[ORM\ManyToOne(cascade:["persist"], fetch:"EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $image= null;

    #[ORM\Column]
    private ?bool $etat = null;

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

    public function getImage(): ?Fichier
    {
        return $this->image;
    }

    public function setImage(?Fichier $image): static
    {
        $this->image = $image;

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
}
