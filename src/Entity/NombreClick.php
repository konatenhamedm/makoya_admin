<?php

namespace App\Entity;

use App\Repository\NombreClickRepository;
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
    private ?Commune $commune = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?SousPrefecture $sousPrefecture = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?Departement $departement = null;

    #[ORM\ManyToOne(inversedBy: 'nombreClicks')]
    private ?Region $region = null;

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

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    public function getSousPrefecture(): ?SousPrefecture
    {
        return $this->sousPrefecture;
    }

    public function setSousPrefecture(?SousPrefecture $sousPrefecture): static
    {
        $this->sousPrefecture = $sousPrefecture;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): static
    {
        $this->departement = $departement;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }
}
