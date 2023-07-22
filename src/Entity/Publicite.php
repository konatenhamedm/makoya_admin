<?php

namespace App\Entity;

use App\Repository\PubliciteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteRepository::class)]
class Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'publicites')]
    private ?Categorie $categorie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\OneToMany(mappedBy: 'publicite', targetEntity: PubliciteImage::class)]
    private Collection $publiciteImages;

    public function __construct()
    {
        $this->publiciteImages = new ArrayCollection();
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * @return Collection<int, PubliciteImage>
     */
    public function getPubliciteImages(): Collection
    {
        return $this->publiciteImages;
    }

    public function addPubliciteImage(PubliciteImage $publiciteImage): static
    {
        if (!$this->publiciteImages->contains($publiciteImage)) {
            $this->publiciteImages->add($publiciteImage);
            $publiciteImage->setPublicite($this);
        }

        return $this;
    }

    public function removePubliciteImage(PubliciteImage $publiciteImage): static
    {
        if ($this->publiciteImages->removeElement($publiciteImage)) {
            // set the owning side to null (unless already changed)
            if ($publiciteImage->getPublicite() === $this) {
                $publiciteImage->setPublicite(null);
            }
        }

        return $this;
    }
}
