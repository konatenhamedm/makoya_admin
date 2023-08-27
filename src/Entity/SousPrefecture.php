<?php

namespace App\Entity;

use App\Repository\SousPrefectureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousPrefectureRepository::class)]
#[ORM\Table(name:'decoupage_sous_prefecture')]
class SousPrefecture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'sousPrefectures')]
    private ?Departement $departement = null;

    #[ORM\OneToMany(mappedBy: 'sousPrefecture', targetEntity: Commune::class)]
    private Collection $communes;

    #[ORM\OneToMany(mappedBy: 'sousPrefecture', targetEntity: NombreClick::class)]
    private Collection $nombreClicks;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
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

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): static
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * @return Collection<int, Commune>
     */
    public function getCommunes(): Collection
    {
        return $this->communes;
    }

    public function addCommune(Commune $commune): static
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->setSousPrefecture($this);
        }

        return $this;
    }

    public function removeCommune(Commune $commune): static
    {
        if ($this->communes->removeElement($commune)) {
            // set the owning side to null (unless already changed)
            if ($commune->getSousPrefecture() === $this) {
                $commune->setSousPrefecture(null);
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
            $nombreClick->setSousPrefecture($this);
        }

        return $this;
    }

    public function removeNombreClick(NombreClick $nombreClick): static
    {
        if ($this->nombreClicks->removeElement($nombreClick)) {
            // set the owning side to null (unless already changed)
            if ($nombreClick->getSousPrefecture() === $this) {
                $nombreClick->setSousPrefecture(null);
            }
        }

        return $this;
    }
}
