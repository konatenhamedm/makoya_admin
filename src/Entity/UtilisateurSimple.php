<?php

namespace App\Entity;

use App\Repository\UtilisateurSimpleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurSimpleRepository::class)]
#[ORM\Table(name: 'user_front_utilisateur_simple')]
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


    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    public function __construct()
    {

        $this->reclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNomComplet(): ?string
    {
        return $this->getNom() . ' ' . $this->getPrenoms();
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
