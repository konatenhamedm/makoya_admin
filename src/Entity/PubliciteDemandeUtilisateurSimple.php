<?php

namespace App\Entity;

use App\Repository\PubliciteDemandeUtilisateurSimpleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteDemandeUtilisateurSimpleRepository::class)]
#[ORM\Table(name: 'publicite_demande_utilisateur_simple')]
class PubliciteDemandeUtilisateurSimple extends Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteDemandeUtilisateurSimples')]
    private ?UtilisateurSimple $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $messageRejeter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?UtilisateurSimple
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?UtilisateurSimple $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
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

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(\DateTimeInterface $dateValidation): static
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getMessageRejeter(): ?string
    {
        return $this->messageRejeter;
    }

    public function setMessageRejeter(?string $messageRejeter): static
    {
        $this->messageRejeter = $messageRejeter;

        return $this;
    }
}
