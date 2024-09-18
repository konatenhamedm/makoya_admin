<?php

namespace App\Entity;

use App\Repository\PrestataireServiceRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Serializer\Annotation\Groups as Group;


#[ORM\Entity(repositoryClass: PrestataireServiceRepository::class)]
#[ORM\Table(name: 'gestion_prestataire_service')]
class PrestataireService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Group(["groupe_commentaire"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    #[Group(["groupe_commentaire"])]
    private ?Prestataire $prestataire = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    //#[Group(["groupe_commentaire"])]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    #[Group(["groupe_commentaire"])]
    private ?ServicePrestataire $service = null;

    #[ORM\ManyToOne(inversedBy: 'prestataireServices')]
    private ?SousCategorie $sousCategorie = null;



    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    #[Group(["groupe_commentaire"])]
    private ?Fichier $image = null;

    #[ORM\Column]
    #[Group(["groupe_commentaire"])]
    private ?bool $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $countVisite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Favorie::class)]
    private Collection $favories;



    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: NombreClick::class)]
    private Collection $nombreClicks;

    public function __construct()
    {
        $this->dateCreation = new DateTime();
        $this->countVisite = 0;
        $this->setCountVisite(0);
        $this->favories = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->nombreClicks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestataire(): ?Prestataire
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Prestataire $prestataire): static
    {
        $this->prestataire = $prestataire;

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

    public function getService(): ?ServicePrestataire
    {
        return $this->service;
    }

    public function setService(?ServicePrestataire $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): static
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
    }

    public function getImage(): ?Fichier
    {
        return $this->image;
    }

    public function setImage(?Fichier $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCountVisite(): ?int
    {
        return $this->countVisite;
    }

    public function setCountVisite(int $countVisite): static
    {
        $this->countVisite = $countVisite;

        return $this;
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

    /**
     * @return Collection<int, Favorie>
     */
    public function getFavories(): Collection
    {
        return $this->favories;
    }

    public function addFavory(Favorie $favory): static
    {
        if (!$this->favories->contains($favory)) {
            $this->favories->add($favory);
            $favory->setService($this);
        }

        return $this;
    }

    public function removeFavory(Favorie $favory): static
    {
        if ($this->favories->removeElement($favory)) {
            // set the owning side to null (unless already changed)
            if ($favory->getService() === $this) {
                $favory->setService(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setService($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getService() === $this) {
                $commentaire->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setService($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getService() === $this) {
                $reclamation->setService(null);
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
            $nombreClick->setService($this);
        }

        return $this;
    }

    public function removeNombreClick(NombreClick $nombreClick): static
    {
        if ($this->nombreClicks->removeElement($nombreClick)) {
            // set the owning side to null (unless already changed)
            if ($nombreClick->getService() === $this) {
                $nombreClick->setService(null);
            }
        }

        return $this;
    }
}
