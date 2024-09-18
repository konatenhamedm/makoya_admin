<?php

namespace App\Entity;

use App\Repository\PharmacieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups as Group;


#[ORM\Entity(repositoryClass: PharmacieRepository::class)]
#[UniqueEntity(fields: 'commune', message: 'Cet enregistrement existe déjà')]
class Pharmacie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Group(["groupe_commentaire"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Group(["groupe_commentaire"])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Group(["groupe_commentaire"])]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'pharmacies')]
    #[Group(["groupe_commentaire"])]
    private ?Commune $commune = null;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    #[Group(["groupe_commentaire"])]
    private ?Fichier $document = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    public function getDocument(): ?Fichier
    {
        return $this->document;
    }

    public function setDocument(?Fichier $document): static
    {
        $this->document = $document;

        return $this;
    }
}
