<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\Table(name: 'reseau_note')]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?UserFront $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?PrestataireService $service = null;

    public function __construct()
    {
        $this->note = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

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
