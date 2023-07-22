<?php

namespace App\Entity;

use App\Repository\UtilisateurSimpleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurSimpleRepository::class)]
#[ORM\Table(name:'user_front_utilisateur_simple')]
class UtilisateurSimple extends UserFront
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurSimples')]
    private ?Civilite $genre = null;

    
    #[ORM\ManyToOne(cascade:["persist"], fetch:"EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $photo= null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): static
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getGenre(): ?Civilite
    {
        return $this->genre;
    }

    public function setGenre(?Civilite $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPhoto(): ?Fichier 
    {
        return $this->photo;
    }

    public function setPhoto(?Fichier  $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
