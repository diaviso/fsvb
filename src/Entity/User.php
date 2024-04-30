<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    /**
     * @var Collection<int, TraceJoueur>
     */
    #[ORM\OneToMany(targetEntity: TraceJoueur::class, mappedBy: 'utilisateur')]
    private Collection $traceJoueurs;

    /**
     * @var Collection<int, Trace>
     */
    #[ORM\OneToMany(targetEntity: Trace::class, mappedBy: 'utilisateur')]
    private Collection $traces;

    /**
     * @var Collection<int, Renouvellement>
     */
    #[ORM\OneToMany(targetEntity: Renouvellement::class, mappedBy: 'utilisateur')]
    private Collection $renouvellements;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Club $club = null;

    #[ORM\OneToOne(mappedBy: 'president', cascade: ['persist', 'remove'])]
    private ?Ligue $ligue = null;

    
    public function __toString()
    {
        return $this->userName;
    }

    

    public function __construct()
    {
        $this->traceJoueurs = new ArrayCollection();
        $this->traces = new ArrayCollection();
        $this->isVerified = true;
        $this->renouvellements = new ArrayCollection();
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
       
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
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

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, TraceJoueur>
     */
    public function getTraceJoueurs(): Collection
    {
        return $this->traceJoueurs;
    }

    public function addTraceJoueur(TraceJoueur $traceJoueur): static
    {
        if (!$this->traceJoueurs->contains($traceJoueur)) {
            $this->traceJoueurs->add($traceJoueur);
            $traceJoueur->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTraceJoueur(TraceJoueur $traceJoueur): static
    {
        if ($this->traceJoueurs->removeElement($traceJoueur)) {
            // set the owning side to null (unless already changed)
            if ($traceJoueur->getUtilisateur() === $this) {
                $traceJoueur->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trace>
     */
    public function getTraces(): Collection
    {
        return $this->traces;
    }

    public function addTrace(Trace $trace): static
    {
        if (!$this->traces->contains($trace)) {
            $this->traces->add($trace);
            $trace->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTrace(Trace $trace): static
    {
        if ($this->traces->removeElement($trace)) {
            // set the owning side to null (unless already changed)
            if ($trace->getUtilisateur() === $this) {
                $trace->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Renouvellement>
     */
    public function getRenouvellements(): Collection
    {
        return $this->renouvellements;
    }

    public function addRenouvellement(Renouvellement $renouvellement): static
    {
        if (!$this->renouvellements->contains($renouvellement)) {
            $this->renouvellements->add($renouvellement);
            $renouvellement->setUtilisateur($this);
        }

        return $this;
    }

    public function removeRenouvellement(Renouvellement $renouvellement): static
    {
        if ($this->renouvellements->removeElement($renouvellement)) {
            // set the owning side to null (unless already changed)
            if ($renouvellement->getUtilisateur() === $this) {
                $renouvellement->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): static
    {
        // unset the owning side of the relation if necessary
        if ($club === null && $this->club !== null) {
            $this->club->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($club !== null && $club->getUser() !== $this) {
            $club->setUser($this);
        }

        $this->club = $club;

        return $this;
    }

    public function getLigue(): ?Ligue
    {
        return $this->ligue;
    }

    public function setLigue(?Ligue $ligue): static
    {
        // unset the owning side of the relation if necessary
        if ($ligue === null && $this->ligue !== null) {
            $this->ligue->setPresident(null);
        }

        // set the owning side of the relation if necessary
        if ($ligue !== null && $ligue->getPresident() !== $this) {
            $ligue->setPresident($this);
        }

        $this->ligue = $ligue;

        return $this;
    }
}
