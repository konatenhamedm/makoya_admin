<?php

namespace App\Entity;

use App\Repository\JoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JoursRepository::class)]
class Jours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: Publicite::class, mappedBy: 'jours')]
    private Collection $publicites;

    public function __construct()
    {
        $this->publicites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Publicite>
     */
    public function getPublicites(): Collection
    {
        return $this->publicites;
    }

    public function addPublicite(Publicite $publicite): static
    {
        if (!$this->publicites->contains($publicite)) {
            $this->publicites->add($publicite);
            $publicite->addJour($this);
        }

        return $this;
    }

    public function removePublicite(Publicite $publicite): static
    {
        if ($this->publicites->removeElement($publicite)) {
            $publicite->removeJour($this);
        }

        return $this;
    }
}
