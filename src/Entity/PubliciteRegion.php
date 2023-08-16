<?php

namespace App\Entity;

use App\Repository\PubliciteRegionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PubliciteRegionRepository::class)]
#[ORM\Table(name: 'publicite_region')]
class PubliciteRegion extends Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publiciteRegions')]
    private ?Region $region = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
