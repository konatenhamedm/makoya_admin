<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: SousCategorie::class)]
    private Collection $sousCategories;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: PrestataireService::class)]
    private Collection $prestataireServices;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Publicite::class)]
    private Collection $publicites;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: ServicePrestataire::class)]
    private Collection $servicePrestataires;

    public function __construct()
    {
        $this->sousCategories = new ArrayCollection();
        $this->prestataireServices = new ArrayCollection();
        $this->publicites = new ArrayCollection();
        $this->servicePrestataires = new ArrayCollection();
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
     * @return Collection<int, Publicite>
     */
    public function getPublicites(): Collection
    {
        return $this->publicites;
    }

    public function addPublicite(Publicite $publicite): static
    {
        if (!$this->publicites->contains($publicite)) {
            $this->publicites->add($publicite);
            $publicite->setCategorie($this);
        }

        return $this;
    }

    public function removePublicite(Publicite $publicite): static
    {
        if ($this->publicites->removeElement($publicite)) {
            // set the owning side to null (unless already changed)
            if ($publicite->getCategorie() === $this) {
                $publicite->setCategorie(null);
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
}
