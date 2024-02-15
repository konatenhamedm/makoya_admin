<?php

namespace App\Entity;

use App\Repository\PubliciteDemandeRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteDemandeRepository::class)]
#[ORM\Table(name: 'publicite_demande_prestataire')]
class PubliciteDemande extends Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 255)]
    private ?string $etat = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datevalidation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $messageRejeter = null;

    #[ORM\Column(nullable: true)]
    private ?int $ordre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nature = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteDemandes')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteDemandes')]
    private ?Region $region = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteDemandes')]
    private ?UserFront $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /* 
    public function __construct()
    {
        $this->dateCreation = new DateTime();
    } */
    public function getId(): ?int
    {
        return $this->id;
    }



    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }


    public function getDatevalidation(): ?\DateTimeInterface
    {
        return $this->datevalidation;
    }

    public function setDatevalidation(\DateTimeInterface $datevalidation): static
    {
        $this->datevalidation = $datevalidation;

        return $this;
    }

    public function getMessageRejeter(): ?string
    {
        return $this->messageRejeter;
    }

    public function setMessageRejeter(string $messageRejeter): static
    {
        $this->messageRejeter = $messageRejeter;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): static
    {
        $this->nature = $nature;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getUtilisateur(): ?UserFront
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?UserFront $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
