<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $motPasse = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $administrateur = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur')]
    private Collection $sortiesOrganisees;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sorties;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $pseudo = null;

    /**
     * @var Collection<int, PhotoProfil>
     */
    #[ORM\OneToMany(targetEntity: PhotoProfil::class, mappedBy: 'participant', orphanRemoval: true)]
    private Collection $photosProfil;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->sorties = new ArrayCollection();
        $this->photosProfil = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->administrateur ? ['ROLE_ADMIN'] : ['ROLE_USER'];
    }

    public function getMotPasse(): string
    {
        return $this->motPasse;
    }

    /**
    * @see PasswordAuthenticatedUserInterface
    */
    public function getPassword(): string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): static
    {
        $this->motPasse = $motPasse;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisees(Sortie $sortie): static
    {
        if (!$this->sortiesOrganisees->contains($sortie)) {
            $this->sortiesOrganisees->add($sortie);
            $sortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisees(Sortie $sortie): static
    {
        if ($this->sortiesOrganisees->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getOrganisateur() === $this) {
                $sortie->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): static
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties->add($sortie);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static
    {
        $this->sorties->removeElement($sortie);

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, PhotoProfil>
     */
    public function getPhotosProfil(): Collection
    {
        return $this->photosProfil;
    }

    public function addPhotosProfil(PhotoProfil $photosProfil): static
    {
        if (!$this->photosProfil->contains($photosProfil)) {
            $this->photosProfil->add($photosProfil);
            $photosProfil->setParticipant($this);
        }

        return $this;
    }

    public function removePhotosProfil(PhotoProfil $photosProfil): static
    {
        if ($this->photosProfil->removeElement($photosProfil)) {
            // set the owning side to null (unless already changed)
            if ($photosProfil->getParticipant() === $this) {
                $photosProfil->setParticipant(null);
            }
        }

        return $this;
    }
}
