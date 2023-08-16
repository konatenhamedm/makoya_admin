<?php

namespace App\Entity;

use App\Repository\UtilisateurSimpleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurSimpleRepository::class)]
#[ORM\Table(name:'user_front_utilisateur_simple')]
class UtilisateurSimple extends UserFront
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurSimples')]
    private ?Civilite $genre = null;

    
    #[ORM\ManyToOne(cascade:["persist"], fetch:"EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $photo= null;

    #[ORM\OneToMany(mappedBy: 'utilisateurSimple', targetEntity: NotificationUtilisateurSimple::class)]
    private Collection $notificationUtilisateurSimples;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: PubliciteDemandeUtilisateurSimple::class)]
    private Collection $publiciteDemandeUtilisateurSimples;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    public function __construct()
    {
        $this->notificationUtilisateurSimples = new ArrayCollection();
        $this->publiciteDemandeUtilisateurSimples = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): static
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getGenre(): ?Civilite
    {
        return $this->genre;
    }

    public function setGenre(?Civilite $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPhoto(): ?Fichier 
    {
        return $this->photo;
    }

    public function setPhoto(?Fichier  $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, NotificationUtilisateurSimple>
     */
    public function getNotificationUtilisateurSimples(): Collection
    {
        return $this->notificationUtilisateurSimples;
    }

    public function addNotificationUtilisateurSimple(NotificationUtilisateurSimple $notificationUtilisateurSimple): static
    {
        if (!$this->notificationUtilisateurSimples->contains($notificationUtilisateurSimple)) {
            $this->notificationUtilisateurSimples->add($notificationUtilisateurSimple);
            $notificationUtilisateurSimple->setUtilisateurSimple($this);
        }

        return $this;
    }

    public function removeNotificationUtilisateurSimple(NotificationUtilisateurSimple $notificationUtilisateurSimple): static
    {
        if ($this->notificationUtilisateurSimples->removeElement($notificationUtilisateurSimple)) {
            // set the owning side to null (unless already changed)
            if ($notificationUtilisateurSimple->getUtilisateurSimple() === $this) {
                $notificationUtilisateurSimple->setUtilisateurSimple(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PubliciteDemandeUtilisateurSimple>
     */
    public function getPubliciteDemandeUtilisateurSimples(): Collection
    {
        return $this->publiciteDemandeUtilisateurSimples;
    }

    public function addPubliciteDemandeUtilisateurSimple(PubliciteDemandeUtilisateurSimple $publiciteDemandeUtilisateurSimple): static
    {
        if (!$this->publiciteDemandeUtilisateurSimples->contains($publiciteDemandeUtilisateurSimple)) {
            $this->publiciteDemandeUtilisateurSimples->add($publiciteDemandeUtilisateurSimple);
            $publiciteDemandeUtilisateurSimple->setUtilisateur($this);
        }

        return $this;
    }

    public function removePubliciteDemandeUtilisateurSimple(PubliciteDemandeUtilisateurSimple $publiciteDemandeUtilisateurSimple): static
    {
        if ($this->publiciteDemandeUtilisateurSimples->removeElement($publiciteDemandeUtilisateurSimple)) {
            // set the owning side to null (unless already changed)
            if ($publiciteDemandeUtilisateurSimple->getUtilisateur() === $this) {
                $publiciteDemandeUtilisateurSimple->setUtilisateur(null);
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
            $reclamation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUtilisateur() === $this) {
                $reclamation->setUtilisateur(null);
            }
        }

        return $this;
    }
}
