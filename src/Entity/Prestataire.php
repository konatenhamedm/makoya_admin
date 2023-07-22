<?php

namespace App\Entity;

use App\Repository\PrestataireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestataireRepository::class)]
#[ORM\Table(name:'user_front_prestataire')]
class Prestataire extends UserFront
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denominationSociale = null;


    #[ORM\ManyToOne(cascade:["persist"], fetch:"EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $logo= null;


    #[ORM\Column(length: 255)]
    private ?string $contactPrincipal = null;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: PrestataireService::class,orphanRemoval: true, cascade:['persist'])]
    private Collection $prestataireServices;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: PropositionService::class)]
    private Collection $propositionServices;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: NumeroPrestataire::class)]
    private Collection $numeroPrestataires;

    public function __construct()
    {
        $this->prestataireServices = new ArrayCollection();
        $this->propositionServices = new ArrayCollection();
        $this->numeroPrestataires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenominationSociale(): ?string
    {
        return $this->denominationSociale;
    }

    public function setDenominationSociale(string $denominationSociale): static
    {
        $this->denominationSociale = $denominationSociale;

        return $this;
    }

    public function getLogo(): ?Fichier
    {
        return $this->logo;
    }

    public function setLogo(?Fichier $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getContactPrincipal(): ?string
    {
        return $this->contactPrincipal;
    }

    public function setContactPrincipal(string $contactPrincipal): static
    {
        $this->contactPrincipal = $contactPrincipal;

        return $this;
    }

    /**
     * @return Collection<int, PrestataireService>
     */
    public function getPrestataireServices(): Collection
    {
        return $this->prestataireServices;
    }

    public function addPrestataireService(PrestataireService $prestataireService): static
    {
        if (!$this->prestataireServices->contains($prestataireService)) {
            $this->prestataireServices->add($prestataireService);
            $prestataireService->setPrestataire($this);
        }

        return $this;
    }

    public function removePrestataireService(PrestataireService $prestataireService): static
    {
        if ($this->prestataireServices->removeElement($prestataireService)) {
            // set the owning side to null (unless already changed)
            if ($prestataireService->getPrestataire() === $this) {
                $prestataireService->setPrestataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PropositionService>
     */
    public function getPropositionServices(): Collection
    {
        return $this->propositionServices;
    }

    public function addPropositionService(PropositionService $propositionService): static
    {
        if (!$this->propositionServices->contains($propositionService)) {
            $this->propositionServices->add($propositionService);
            $propositionService->setPrestataire($this);
        }

        return $this;
    }

    public function removePropositionService(PropositionService $propositionService): static
    {
        if ($this->propositionServices->removeElement($propositionService)) {
            // set the owning side to null (unless already changed)
            if ($propositionService->getPrestataire() === $this) {
                $propositionService->setPrestataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NumeroPrestataire>
     */
    public function getNumeroPrestataires(): Collection
    {
        return $this->numeroPrestataires;
    }

    public function addNumeroPrestataire(NumeroPrestataire $numeroPrestataire): static
    {
        if (!$this->numeroPrestataires->contains($numeroPrestataire)) {
            $this->numeroPrestataires->add($numeroPrestataire);
            $numeroPrestataire->setPrestataire($this);
        }

        return $this;
    }

    public function removeNumeroPrestataire(NumeroPrestataire $numeroPrestataire): static
    {
        if ($this->numeroPrestataires->removeElement($numeroPrestataire)) {
            // set the owning side to null (unless already changed)
            if ($numeroPrestataire->getPrestataire() === $this) {
                $numeroPrestataire->setPrestataire(null);
            }
        }

        return $this;
    }
}
