<?php

namespace App\Entity;

use App\Repository\NombreClickRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NombreClickRepository::class)]
#[ORM\Table(name: 'param_nombre_click_service')]
class NombreClick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?PrestataireService $service = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?Quartier $quartier = null;

    #[ORM\Column]
    private ?int $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?SousCategorie $sousCategorie = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $mac = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModification = null;


    public function __construct()
    {
        $this->dateModification = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): static
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): static
    {
        $this->nombre = $nombre;

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

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): static
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(string $mac): static
    {
        $this->mac = $mac;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
