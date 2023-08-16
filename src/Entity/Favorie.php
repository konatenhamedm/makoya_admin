<?php

namespace App\Entity;

use App\Repository\FavorieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavorieRepository::class)]
#[ORM\Table(name: 'reseau_favorie')]
class Favorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\ManyToOne(inversedBy: 'favories')]
    private ?UserFront $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'favories')]
    private ?PrestataireService $service = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtilisateur(): ?UserFront
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?UserFront $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getService(): ?PrestataireService
    {
        return $this->service;
    }

    public function setService(?PrestataireService $service): static
    {
        $this->service = $service;

        return $this;
    }
}
