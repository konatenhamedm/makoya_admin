<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\Table(name: 'decoupage_regions')]
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



    #[ORM\OneToMany(mappedBy: 'region', targetEntity: PubliciteDemande::class)]
    private Collection $publiciteDemandes;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: PublicitePrestataire::class)]
    private Collection $publicitePrestataires;

    public function __construct()
    {
        $this->departements = new ArrayCollection();
        $this->publiciteRegions = new ArrayCollection();

        $this->publiciteDemandes = new ArrayCollection();

        $this->publicitePrestataires = new ArrayCollection();
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
     * @return Collection<int, PubliciteDemande>
     */
    public function getPubliciteDemandes(): Collection
    {
        return $this->publiciteDemandes;
    }

    public function addPubliciteDemande(PubliciteDemande $publiciteDemande): static
    {
        if (!$this->publiciteDemandes->contains($publiciteDemande)) {
            $this->publiciteDemandes->add($publiciteDemande);
            $publiciteDemande->setRegion($this);
        }

        return $this;
    }

    public function removePubliciteDemande(PubliciteDemande $publiciteDemande): static
    {
        if ($this->publiciteDemandes->removeElement($publiciteDemande)) {
            // set the owning side to null (unless already changed)
            if ($publiciteDemande->getRegion() === $this) {
                $publiciteDemande->setRegion(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, PublicitePrestataire>
     */
    public function getPublicitePrestataires(): Collection
    {
        return $this->publicitePrestataires;
    }

    public function addPublicitePrestataire(PublicitePrestataire $publicitePrestataire): static
    {
        if (!$this->publicitePrestataires->contains($publicitePrestataire)) {
            $this->publicitePrestataires->add($publicitePrestataire);
            $publicitePrestataire->setRegion($this);
        }

        return $this;
    }

    public function removePublicitePrestataire(PublicitePrestataire $publicitePrestataire): static
    {
        if ($this->publicitePrestataires->removeElement($publicitePrestataire)) {
            // set the owning side to null (unless already changed)
            if ($publicitePrestataire->getRegion() === $this) {
                $publicitePrestataire->setRegion(null);
            }
        }

        return $this;
    }
}
