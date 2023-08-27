<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ORM\Table(name:'decoupage_departement')]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'departements')]
    private ?Region $region = null;

    #[ORM\OneToMany(mappedBy: 'departement', targetEntity: SousPrefecture::class)]
    private Collection $sousPrefectures;

    #[ORM\OneToMany(mappedBy: 'departement', targetEntity: NombreClick::class)]
    private Collection $nombreClicks;

    public function __construct()
    {
        $this->sousPrefectures = new ArrayCollection();
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, SousPrefecture>
     */
    public function getSousPrefectures(): Collection
    {
        return $this->sousPrefectures;
    }

    public function addSousPrefecture(SousPrefecture $sousPrefecture): static
    {
        if (!$this->sousPrefectures->contains($sousPrefecture)) {
            $this->sousPrefectures->add($sousPrefecture);
            $sousPrefecture->setDepartement($this);
        }

        return $this;
    }

    public function removeSousPrefecture(SousPrefecture $sousPrefecture): static
    {
        if ($this->sousPrefectures->removeElement($sousPrefecture)) {
            // set the owning side to null (unless already changed)
            if ($sousPrefecture->getDepartement() === $this) {
                $sousPrefecture->setDepartement(null);
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
            $nombreClick->setDepartement($this);
        }

        return $this;
    }

    public function removeNombreClick(NombreClick $nombreClick): static
    {
        if ($this->nombreClicks->removeElement($nombreClick)) {
            // set the owning side to null (unless already changed)
            if ($nombreClick->getDepartement() === $this) {
                $nombreClick->setDepartement(null);
            }
        }

        return $this;
    }
}
