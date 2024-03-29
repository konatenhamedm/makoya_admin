<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\Table(name:'decoupage_regions')]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'regions')]
    private ?Pays $pays = null;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Departement::class)]
    private Collection $departements;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: PubliciteRegion::class)]
    private Collection $publiciteRegions;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: NombreClick::class)]
    private Collection $nombreClicks;

    public function __construct()
    {
        $this->departements = new ArrayCollection();
        $this->publiciteRegions = new ArrayCollection();
        $this->nombreClicks = new ArrayCollection();
    }

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection<int, Departement>
     */
    public function getDepartements(): Collection
    {
        return $this->departements;
    }

    public function addDepartement(Departement $departement): static
    {
        if (!$this->departements->contains($departement)) {
            $this->departements->add($departement);
            $departement->setRegion($this);
        }

        return $this;
    }

    public function removeDepartement(Departement $departement): static
    {
        if ($this->departements->removeElement($departement)) {
            // set the owning side to null (unless already changed)
            if ($departement->getRegion() === $this) {
                $departement->setRegion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PubliciteRegion>
     */
    public function getPubliciteRegions(): Collection
    {
        return $this->publiciteRegions;
    }

    public function addPubliciteRegion(PubliciteRegion $publiciteRegion): static
    {
        if (!$this->publiciteRegions->contains($publiciteRegion)) {
            $this->publiciteRegions->add($publiciteRegion);
            $publiciteRegion->setRegion($this);
        }

        return $this;
    }

    public function removePubliciteRegion(PubliciteRegion $publiciteRegion): static
    {
        if ($this->publiciteRegions->removeElement($publiciteRegion)) {
            // set the owning side to null (unless already changed)
            if ($publiciteRegion->getRegion() === $this) {
                $publiciteRegion->setRegion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NombreClick>
     */
    public function getNombreClicks(): Collection
    {
        return $this->nombreClicks;
    }

    public function addNombreClick(NombreClick $nombreClick): static
    {
        if (!$this->nombreClicks->contains($nombreClick)) {
            $this->nombreClicks->add($nombreClick);
            $nombreClick->setRegion($this);
        }

        return $this;
    }

    public function removeNombreClick(NombreClick $nombreClick): static
    {
        if ($this->nombreClicks->removeElement($nombreClick)) {
            // set the owning side to null (unless already changed)
            if ($nombreClick->getRegion() === $this) {
                $nombreClick->setRegion(null);
            }
        }

        return $this;
    }
}
