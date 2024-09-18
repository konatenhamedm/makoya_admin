<?php

namespace App\Entity;

use App\Repository\CommuneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as Group;

#[ORM\Entity(repositoryClass: CommuneRepository::class)]
#[ORM\Table(name: 'decoupage_commune')]
class Commune
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Group(["groupe_commentaire"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Group(["groupe_commentaire"])]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'commune', targetEntity: Quartier::class)]
    private Collection $quartiers;

    #[ORM\ManyToOne(inversedBy: 'communes')]
    private ?SousPrefecture $sousPrefecture = null;

    #[ORM\OneToMany(mappedBy: 'commune', targetEntity: Pharmacie::class)]
    private Collection $pharmacies;



    public function __construct()
    {
        $this->quartiers = new ArrayCollection();
        $this->pharmacies = new ArrayCollection();
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

    /**
     * @return Collection<int, Quartier>
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): static
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers->add($quartier);
            $quartier->setCommune($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): static
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getCommune() === $this) {
                $quartier->setCommune(null);
            }
        }

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

    /**
     * @return Collection<int, Pharmacie>
     */
    public function getPharmacies(): Collection
    {
        return $this->pharmacies;
    }

    public function addPharmacy(Pharmacie $pharmacy): static
    {
        if (!$this->pharmacies->contains($pharmacy)) {
            $this->pharmacies->add($pharmacy);
            $pharmacy->setCommune($this);
        }

        return $this;
    }

    public function removePharmacy(Pharmacie $pharmacy): static
    {
        if ($this->pharmacies->removeElement($pharmacy)) {
            // set the owning side to null (unless already changed)
            if ($pharmacy->getCommune() === $this) {
                $pharmacy->setCommune(null);
            }
        }

        return $this;
    }
}
