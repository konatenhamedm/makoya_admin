<?php

namespace App\Entity;

use App\Repository\UserFrontRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups as Group;


use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserFrontRepository::class)]
#[ORM\Table(name: 'user_front_utilisateur')]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
/* #[DiscriminatorMap(['prestataire' => Prestataire::class, 'utilisateursimple' => UtilisateurSimple::class])] */
#[UniqueEntity(fields: 'email', message: 'Cet email utilisateur est déjà associé à un compte')]
class UserFront implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Group(["groupe_commentaire"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un pseudo')]
    #[Group(["groupe_commentaire"])]
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
    #[Group(["groupe_commentaire"])]
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


    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Signaler::class)]
    private Collection $signalers;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDesactivation = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: PubliciteDemande::class)]
    private Collection $publiciteDemandes;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: NotificationPrestataire::class)]
    private Collection $notificationPrestataires;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Sponsoring::class)]
    private Collection $sponsorings;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $userAdd = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    #[Group(["groupe_commentaire"])]
    private ?Fichier $photo = null;



    public function __construct()
    {
        $this->publicitePrestataires = new ArrayCollection();
        $this->favories = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->signalers = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->publiciteDemandes = new ArrayCollection();
        $this->notificationPrestataires = new ArrayCollection();
        $this->sponsorings = new ArrayCollection();
    }

    public function getEmailUser()
    {
        return $this->username . '-' . $this->email;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): ?Fichier
    {
        return $this->photo;
    }

    public function setPhoto(?Fichier  $photo): static
    {
        $this->photo = $photo;

        return $this;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateDesactivation(): ?\DateTimeInterface
    {
        return $this->dateDesactivation;
    }

    public function setDateDesactivation(\DateTimeInterface $dateDesactivation): static
    {
        $this->dateDesactivation = $dateDesactivation;

        return $this;
    }

    /**
     * @return Collection<int, PubliciteDemande>
     */
    public function getPubliciteDemandes(): Collection
    {
        return $this->publiciteDemandes;
    }

    public function addPubliciteDemande(PubliciteDemande $publiciteDemande): static
    {
        if (!$this->publiciteDemandes->contains($publiciteDemande)) {
            $this->publiciteDemandes->add($publiciteDemande);
            $publiciteDemande->setUtilisateur($this);
        }

        return $this;
    }

    public function removePubliciteDemande(PubliciteDemande $publiciteDemande): static
    {
        if ($this->publiciteDemandes->removeElement($publiciteDemande)) {
            // set the owning side to null (unless already changed)
            if ($publiciteDemande->getUtilisateur() === $this) {
                $publiciteDemande->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NotificationPrestataire>
     */
    public function sendNotificationPrestataires(): Collection
    {
        return $this->notificationPrestataires;
    }

    public function addNotificationPrestataire(NotificationPrestataire $notificationPrestataire): static
    {
        if (!$this->notificationPrestataires->contains($notificationPrestataire)) {
            $this->notificationPrestataires->add($notificationPrestataire);
            $notificationPrestataire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeNotificationPrestataire(NotificationPrestataire $notificationPrestataire): static
    {
        if ($this->notificationPrestataires->removeElement($notificationPrestataire)) {
            // set the owning side to null (unless already changed)
            if ($notificationPrestataire->getUtilisateur() === $this) {
                $notificationPrestataire->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sponsoring>
     */
    public function getSponsorings(): Collection
    {
        return $this->sponsorings;
    }

    public function addSponsoring(Sponsoring $sponsoring): static
    {
        if (!$this->sponsorings->contains($sponsoring)) {
            $this->sponsorings->add($sponsoring);
            $sponsoring->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSponsoring(Sponsoring $sponsoring): static
    {
        if ($this->sponsorings->removeElement($sponsoring)) {
            // set the owning side to null (unless already changed)
            if ($sponsoring->getUtilisateur() === $this) {
                $sponsoring->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getUserAdd(): ?Utilisateur
    {
        return $this->userAdd;
    }

    public function setUserAdd(?Utilisateur $userAdd): static
    {
        $this->userAdd = $userAdd;

        return $this;
    }
}
