<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[UniqueEntity(['slug'], message: 'Ce slug est déjà utilisé')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner le libelle")]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: SousCategorie::class)]
    private Collection $sousCategories;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: PrestataireService::class)]
    private Collection $prestataireServices;


    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: ServicePrestataire::class)]
    private Collection $servicePrestataires;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: WorkflowServicePrestataire::class)]
    private Collection $workflowServicePrestataires;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: PubliciteCategorie::class)]
    private Collection $publiciteCategories;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: "Veuillez renseigner l'image de la catégorie")]
    private ?Fichier $imageLaUne = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: PropositionService::class)]
    private Collection $propositionServices;
    /*@Gedmo\Slug(fields={"titre"})*/
    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ["libelle"])]
    private ?string $slug = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $image = null;

    public function __construct()
    {
        $this->sousCategories = new ArrayCollection();
        $this->prestataireServices = new ArrayCollection();

        $this->servicePrestataires = new ArrayCollection();
        $this->workflowServicePrestataires = new ArrayCollection();
        $this->publiciteCategories = new ArrayCollection();
        $this->propositionServices = new ArrayCollection();
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

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, SousCategorie>
     */
    public function getSousCategories(): Collection
    {
        return $this->sousCategories;
    }

    public function addSousCategory(SousCategorie $sousCategory): static
    {
        if (!$this->sousCategories->contains($sousCategory)) {
            $this->sousCategories->add($sousCategory);
            $sousCategory->setCategorie($this);
        }

        return $this;
    }

    public function removeSousCategory(SousCategorie $sousCategory): static
    {
        if ($this->sousCategories->removeElement($sousCategory)) {
            // set the owning side to null (unless already changed)
            if ($sousCategory->getCategorie() === $this) {
                $sousCategory->setCategorie(null);
            }
        }

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
            $prestataireService->setCategorie($this);
        }

        return $this;
    }

    public function removePrestataireService(PrestataireService $prestataireService): static
    {
        if ($this->prestataireServices->removeElement($prestataireService)) {
            // set the owning side to null (unless already changed)
            if ($prestataireService->getCategorie() === $this) {
                $prestataireService->setCategorie(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, ServicePrestataire>
     */
    public function getServicePrestataires(): Collection
    {
        return $this->servicePrestataires;
    }

    public function addServicePrestataire(ServicePrestataire $servicePrestataire): static
    {
        if (!$this->servicePrestataires->contains($servicePrestataire)) {
            $this->servicePrestataires->add($servicePrestataire);
            $servicePrestataire->setCategorie($this);
        }

        return $this;
    }

    public function removeServicePrestataire(ServicePrestataire $servicePrestataire): static
    {
        if ($this->servicePrestataires->removeElement($servicePrestataire)) {
            // set the owning side to null (unless already changed)
            if ($servicePrestataire->getCategorie() === $this) {
                $servicePrestataire->setCategorie(null);
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
            $workflowServicePrestataire->setCategorie($this);
        }

        return $this;
    }

    public function removeWorkflowServicePrestataire(WorkflowServicePrestataire $workflowServicePrestataire): static
    {
        if ($this->workflowServicePrestataires->removeElement($workflowServicePrestataire)) {
            // set the owning side to null (unless already changed)
            if ($workflowServicePrestataire->getCategorie() === $this) {
                $workflowServicePrestataire->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PubliciteCategorie>
     */
    public function getPubliciteCategories(): Collection
    {
        return $this->publiciteCategories;
    }

    public function addPubliciteCategory(PubliciteCategorie $publiciteCategory): static
    {
        if (!$this->publiciteCategories->contains($publiciteCategory)) {
            $this->publiciteCategories->add($publiciteCategory);
            $publiciteCategory->setCategorie($this);
        }

        return $this;
    }

    public function removePubliciteCategory(PubliciteCategorie $publiciteCategory): static
    {
        if ($this->publiciteCategories->removeElement($publiciteCategory)) {
            // set the owning side to null (unless already changed)
            if ($publiciteCategory->getCategorie() === $this) {
                $publiciteCategory->setCategorie(null);
            }
        }

        return $this;
    }

    public function getImageLaUne(): ?Fichier
    {
        return $this->imageLaUne;
    }

    public function setImageLaUne(?Fichier $imageLaUne): self
    {
        $this->imageLaUne = $imageLaUne;

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
            $propositionService->setCategorie($this);
        }

        return $this;
    }

    public function removePropositionService(PropositionService $propositionService): static
    {
        if ($this->propositionServices->removeElement($propositionService)) {
            // set the owning side to null (unless already changed)
            if ($propositionService->getCategorie() === $this) {
                $propositionService->setCategorie(null);
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

    public function getImage(): ?Fichier
    {
        return $this->image;
    }

    public function setImage(?Fichier $image): self
    {
        $this->image = $image;

        return $this;
    }
}
