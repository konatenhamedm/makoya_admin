<?php

namespace App\Entity;

use App\Repository\PubliciteRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: PubliciteRepository::class)]
#[ORM\Table(name: 'publicite')]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[UniqueEntity(fields: 'code', message: 'Une publicité existe deja avec ce code')]
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\OneToMany(mappedBy: 'publicite', targetEntity: PubliciteImage::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $publiciteImages;

    #[ORM\ManyToMany(targetEntity: Jours::class, inversedBy: 'publicites')]
    private Collection $jours;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;


    public function __construct()
    {
        $this->publiciteImages = new ArrayCollection();
        $this->jours = new ArrayCollection();
        $this->dateCreation = new DateTime();
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    /**
     * @return Collection<int, Jours>
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(Jours $jour): static
    {
        if (!$this->jours->contains($jour)) {
            $this->jours->add($jour);
        }

        return $this;
    }

    public function removeJour(Jours $jour): static
    {
        $this->jours->removeElement($jour);

        return $this;
    }
}
