<?php

namespace App\Entity;

use App\Repository\CiviliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Attribute\Source;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: CiviliteRepository::class)]
#[UniqueEntity(['code'], message: 'Ce code est déjà utilisé')]
#[ORM\Table(name:'param_civilite')]
#[Source]
class Civilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $libelle = null;

    #[ORM\Column(length: 5)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: UtilisateurSimple::class)]
    private Collection $utilisateurSimples;


    public function __construct()
    {
        $this->utilisateurSimples = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, UtilisateurSimple>
     */
    public function getUtilisateurSimples(): Collection
    {
        return $this->utilisateurSimples;
    }

    public function addUtilisateurSimple(UtilisateurSimple $utilisateurSimple): static
    {
        if (!$this->utilisateurSimples->contains($utilisateurSimple)) {
            $this->utilisateurSimples->add($utilisateurSimple);
            $utilisateurSimple->setGenre($this);
        }

        return $this;
    }

    public function removeUtilisateurSimple(UtilisateurSimple $utilisateurSimple): static
    {
        if ($this->utilisateurSimples->removeElement($utilisateurSimple)) {
            // set the owning side to null (unless already changed)
            if ($utilisateurSimple->getGenre() === $this) {
                $utilisateurSimple->setGenre(null);
            }
        }

        return $this;
    }

    
}
