<?php

namespace App\Entity;

use App\Repository\PublicitePrestataireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicitePrestataireRepository::class)]
#[ORM\Table(name: 'publicite_user_front')]
class PublicitePrestataire extends Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'publicitePrestataires')]
    private ?UserFront $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
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
