<?php

namespace App\Entity;

use App\Repository\PubliciteImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteImageRepository::class)]
class PubliciteImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $image = null;


    #[ORM\ManyToOne(inversedBy: 'publiciteImages')]
    private ?Publicite $publicite = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublicite(): ?Publicite
    {
        return $this->publicite;
    }

    public function setPublicite(?Publicite $publicite): static
    {
        $this->publicite = $publicite;

        return $this;
    }
}
