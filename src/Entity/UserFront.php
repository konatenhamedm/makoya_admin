<?php

namespace App\Entity;

use App\Repository\UserFrontRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserFrontRepository::class)]
#[ORM\Table(name: 'user_front_utilisateur')]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[UniqueEntity(fields: 'username', message: 'Ce nom utilisateur est déjà associé à un compte')]
class UserFront implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un pseudo')]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez le mail')]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'userFronts')]
    private ?Quartier $quartier = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lattitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longitude = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: PublicitePrestataire::class)]
    private Collection $publicitePrestataires;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Favorie::class)]
    private Collection $favories;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Note::class)]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Signaler::class)]
    private Collection $signalers;

    public function __construct()
    {
        $this->publicitePrestataires = new ArrayCollection();
        $this->favories = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->signalers = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): static
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getLattitude(): ?string
    {
        return $this->lattitude;
    }

    public function setLattitude(string $lattitude): static
    {
        $this->lattitude = $lattitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, PublicitePrestataire>
     */
    public function getPublicitePrestataires(): Collection
    {
        return $this->publicitePrestataires;
    }

    public function addPublicitePrestataire(PublicitePrestataire $publicitePrestataire): static
    {
        if (!$this->publicitePrestataires->contains($publicitePrestataire)) {
            $this->publicitePrestataires->add($publicitePrestataire);
            $publicitePrestataire->setUtilisateur($this);
        }

        return $this;
    }

    public function removePublicitePrestataire(PublicitePrestataire $publicitePrestataire): static
    {
        if ($this->publicitePrestataires->removeElement($publicitePrestataire)) {
            // set the owning side to null (unless already changed)
            if ($publicitePrestataire->getUtilisateur() === $this) {
                $publicitePrestataire->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorie>
     */
    public function getFavories(): Collection
    {
        return $this->favories;
    }

    public function addFavory(Favorie $favory): static
    {
        if (!$this->favories->contains($favory)) {
            $this->favories->add($favory);
            $favory->setUtilisateur($this);
        }

        return $this;
    }

    public function removeFavory(Favorie $favory): static
    {
        if ($this->favories->removeElement($favory)) {
            // set the owning side to null (unless already changed)
            if ($favory->getUtilisateur() === $this) {
                $favory->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUtilisateur($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUtilisateur() === $this) {
                $note->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Signaler>
     */
    public function getSignalers(): Collection
    {
        return $this->signalers;
    }

    public function addSignaler(Signaler $signaler): static
    {
        if (!$this->signalers->contains($signaler)) {
            $this->signalers->add($signaler);
            $signaler->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSignaler(Signaler $signaler): static
    {
        if ($this->signalers->removeElement($signaler)) {
            // set the owning side to null (unless already changed)
            if ($signaler->getUtilisateur() === $this) {
                $signaler->setUtilisateur(null);
            }
        }

        return $this;
    }
}
