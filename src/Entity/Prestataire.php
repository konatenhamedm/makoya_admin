<?php

namespace App\Entity;

use App\Repository\PrestataireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as Group;


#[ORM\Entity(repositoryClass: PrestataireRepository::class)]
#[ORM\Table(name: 'user_front_prestataire')]
class Prestataire extends UserFront
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Group(["groupe_commentaire"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Group(["groupe_commentaire"])]
    private ?string $denominationSociale = null;




    #[ORM\Column(length: 255)]
    #[Group(["groupe_commentaire"])]
    private ?string $contactPrincipal = null;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: PrestataireService::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $prestataireServices;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: PropositionService::class)]
    private Collection $propositionServices;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: NumeroPrestataire::class)]
    private Collection $numeroPrestataires;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: WorkflowServicePrestataire::class)]
    private Collection $workflowServicePrestataires;





    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Signaler::class)]
    private Collection $signalers;

    #[ORM\Column(length: 5, nullable: true)]
    #[Group(["groupe_commentaire"])]
    private ?string $statut = null;

    public function __construct()
    {
        $this->prestataireServices = new ArrayCollection();
        $this->propositionServices = new ArrayCollection();
        $this->numeroPrestataires = new ArrayCollection();
        $this->workflowServicePrestataires = new ArrayCollection();
        $this->signalers = new ArrayCollection();
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

    /**
     * @return Collection<int, WorkflowServicePrestataire>
     */
    public function getWorkflowServicePrestataires(): Collection
    {
        return $this->workflowServicePrestataires;
    }

    public function addWorkflowServicePrestataire(WorkflowServicePrestataire $workflowServicePrestataire): static
    {
        if (!$this->workflowServicePrestataires->contains($workflowServicePrestataire)) {
            $this->workflowServicePrestataires->add($workflowServicePrestataire);
            $workflowServicePrestataire->setPrestataire($this);
        }

        return $this;
    }

    public function removeWorkflowServicePrestataire(WorkflowServicePrestataire $workflowServicePrestataire): static
    {
        if ($this->workflowServicePrestataires->removeElement($workflowServicePrestataire)) {
            // set the owning side to null (unless already changed)
            if ($workflowServicePrestataire->getPrestataire() === $this) {
                $workflowServicePrestataire->setPrestataire(null);
            }
        }

        return $this;
    }






    /**
     * @return Collection<int, Signaler>
     */
    public function getSignalers(): Collection
    {
        return $this->signalers;
    }

    public function addSignaler(Signaler $signaler): static
    {
        if (!$this->signalers->contains($signaler)) {
            $this->signalers->add($signaler);
            $signaler->setPrestataire($this);
        }

        return $this;
    }

    public function removeSignaler(Signaler $signaler): static
    {
        if ($this->signalers->removeElement($signaler)) {
            // set the owning side to null (unless already changed)
            if ($signaler->getPrestataire() === $this) {
                $signaler->setPrestataire(null);
            }
        }

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
