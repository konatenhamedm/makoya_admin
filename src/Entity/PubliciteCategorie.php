<?php

namespace App\Entity;

use App\Repository\PubliciteCategorieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteCategorieRepository::class)]
#[ORM\Table(name: 'publicite_categorie')]
/*
*Il s'agit des publication par categorie
*/
class PubliciteCategorie extends Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteCategories')]
    private ?Categorie $categorie = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
