<?php

namespace App\Entity;

use App\Repository\SousCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: SousCategorieRepository::class)]
#[ORM\Table(name: 'param_sous_categorie')]
class SousCategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $image = null;

    #[ORM\ManyToOne(inversedBy: 'sousCategories')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'sousCategorie', targetEntity: PrestataireService::class)]
    private Collection $prestataireServices;

    #[ORM\OneToMany(mappedBy: 'sousCategorie', targetEntity: WorkflowServicePrestataire::class)]
    private Collection $workflowServicePrestataires;

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ["libelle"])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'sousCategorie', targetEntity: NombreClick::class)]
    private Collection $nombreClicks;

    public function __construct()
    {
        $this->prestataireServices = new ArrayCollection();
        $this->workflowServicePrestataires = new ArrayCollection();
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function getImage(): ?Fichier
    {
        return $this->image;
    }

    public function setImage(?Fichier $image): self
    {
        $this->image = $image;

        return $this;
    }
    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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
            $prestataireService->setSousCategorie($this);
        }

        return $this;
    }

    public function removePrestataireService(PrestataireService $prestataireService): static
    {
        if ($this->prestataireServices->removeElement($prestataireService)) {
            // set the owning side to null (unless already changed)
            if ($prestataireService->getSousCategorie() === $this) {
                $prestataireService->setSousCategorie(null);
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
            $workflowServicePrestataire->setSousCategorie($this);
        }

        return $this;
    }

    public function removeWorkflowServicePrestataire(WorkflowServicePrestataire $workflowServicePrestataire): static
    {
        if ($this->workflowServicePrestataires->removeElement($workflowServicePrestataire)) {
            // set the owning side to null (unless already changed)
            if ($workflowServicePrestataire->getSousCategorie() === $this) {
                $workflowServicePrestataire->setSousCategorie(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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
            $nombreClick->setSousCategorie($this);
        }

        return $this;
    }

    public function removeNombreClick(NombreClick $nombreClick): static
    {
        if ($this->nombreClicks->removeElement($nombreClick)) {
            // set the owning side to null (unless already changed)
            if ($nombreClick->getSousCategorie() === $this) {
                $nombreClick->setSousCategorie(null);
            }
        }

        return $this;
    }
}
