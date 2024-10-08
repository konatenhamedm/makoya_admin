<?php

namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as Group;


#[ORM\Entity(repositoryClass: QuartierRepository::class)]
#[ORM\Table(name: 'decoupage_quartier')]
class Quartier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'quartiers')]
    private ?Commune $commune = null;

    #[ORM\OneToMany(mappedBy: 'quartier', targetEntity: UserFront::class)]
    private Collection $userFronts;

    #[ORM\OneToMany(mappedBy: 'quartier', targetEntity: NombreClick::class)]
    private Collection $nombreClicks;


    #[ORM\OneToMany(mappedBy: 'quartier', targetEntity: Sponsoring::class)]
    private Collection $sponsorings;

    #[Group(["groupe_commentaire"])]
    private ?string $villeQuartier = null;

    public function __construct()
    {
        $this->userFronts = new ArrayCollection();
        $this->nombreClicks = new ArrayCollection();
        $this->sponsorings = new ArrayCollection();
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
    public function  getNomComplet()
    {
        return $this->nom . ' - ' . $this->getCommune()->getNom() . ' - ' . $this->getCommune()->getSousPrefecture()->getDepartement()->getRegion()->getNom();
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

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    /**
     * @return Collection<int, UserFront>
     */
    public function getUserFronts(): Collection
    {
        return $this->userFronts;
    }

    public function addUserFront(UserFront $userFront): static
    {
        if (!$this->userFronts->contains($userFront)) {
            $this->userFronts->add($userFront);
            $userFront->setQuartier($this);
        }

        return $this;
    }

    public function removeUserFront(UserFront $userFront): static
    {
        if ($this->userFronts->removeElement($userFront)) {
            // set the owning side to null (unless already changed)
            if ($userFront->getQuartier() === $this) {
                $userFront->setQuartier(null);
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
            $nombreClick->setQuartier($this);
        }

        return $this;
    }

    public function removeNombreClick(NombreClick $nombreClick): static
    {
        if ($this->nombreClicks->removeElement($nombreClick)) {
            // set the owning side to null (unless already changed)
            if ($nombreClick->getQuartier() === $this) {
                $nombreClick->setQuartier(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Sponsoring>
     */
    public function getSponsorings(): Collection
    {
        return $this->sponsorings;
    }

    public function addSponsoring(Sponsoring $sponsoring): static
    {
        if (!$this->sponsorings->contains($sponsoring)) {
            $this->sponsorings->add($sponsoring);
            $sponsoring->setQuartier($this);
        }

        return $this;
    }

    public function removeSponsoring(Sponsoring $sponsoring): static
    {
        if ($this->sponsorings->removeElement($sponsoring)) {
            // set the owning side to null (unless already changed)
            if ($sponsoring->getQuartier() === $this) {
                $sponsoring->setQuartier(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of villeQuartier
     */
    public function getVilleQuartier()
    {
        return   $this->getCommune()->getNom() . ' - ' . $this->getNom();
    }
}
