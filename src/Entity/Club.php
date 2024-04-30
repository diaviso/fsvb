<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'clubs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ligue $ligue = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telephone = null;

 
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $abreviation = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $generatedPassword = null;

    
    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Affiliation::class)]
    private Collection $affiliations;


    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Joueur::class)]
    private Collection $joueurs;

    #[ORM\OneToOne(inversedBy: 'club', cascade: ['persist', 'remove'])]
    private ?User $user = null;

  

    public function __toString()
    {
        return $this->nom;
    }
    
    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->affiliations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLigue(): ?Ligue
    {
        return $this->ligue;
    }

    public function setLigue(?Ligue $ligue): static
    {
        $this->ligue = $ligue;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

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


    public function getAbreviation(): ?string
    {
        return $this->abreviation;
    }

    public function setAbreviation(?string $abreviation): static
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    public function getGeneratedPassword(): ?string
    {
        return $this->generatedPassword;
    }

    public function setGeneratedPassword(?string $generatedPassword): static
    {
        $this->generatedPassword = $generatedPassword;

        return $this;
    }
      /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setClub($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getClub() === $this) {
                $joueur->setClub(null);
            }
        }

        return $this;
    }

    
    /**
     * @return Collection<int, Affiliation>
     */
    public function getAffiliations(): Collection
    {
        return $this->affiliations;
    }

    public function addAffiliation(Affiliation $affiliation): self
    {
        if (!$this->affiliations->contains($affiliation)) {
            $this->affiliations->add($affiliation);
            $affiliation->setClub($this);
        }

        return $this;
    }

    public function removeAffiliation(Affiliation $affiliation): self
    {
        if ($this->affiliations->removeElement($affiliation)) {
            // set the owning side to null (unless already changed)
            if ($affiliation->getClub() === $this) {
                $affiliation->setClub(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    

    
}
