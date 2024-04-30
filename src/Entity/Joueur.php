<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDeNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuDenaissance = null;

    #[ORM\Column(length: 20)]
    private ?string $sexe = null;

    #[ORM\Column(length: 80)]
    private ?string $nationalite = null;

    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 120)]
    private ?string $telephone = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $numero = null;

    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Club $club = null;

    #[ORM\Column(length: 120)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?bool $isAccepted = null;

    /**
     * @var Collection<int, TraceJoueur>
     */
    #[ORM\OneToMany(targetEntity: TraceJoueur::class, mappedBy: 'joueur')]
    private Collection $traceJoueurs;

    /**
     * @var Collection<int, Renouvellement>
     */
    #[ORM\OneToMany(targetEntity: Renouvellement::class, mappedBy: 'joueur')]
    private Collection $renouvellements;

    public function __toString()
    {
        return $this->numero.' '.$this->prenom.' '.$this->nom;
    }
    
    public function __construct()
    {
        $this->isAccepted == false;
        $this->traceJoueurs = new ArrayCollection();
        $this->renouvellements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): self
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    public function getLieuDenaissance(): ?string
    {
        return $this->lieuDenaissance;
    }

    public function setLieuDenaissance(string $lieuDenaissance): self
    {
        $this->lieuDenaissance = $lieuDenaissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function isIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

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
            $traceJoueur->setJoueur($this);
        }

        return $this;
    }

    public function removeTraceJoueur(TraceJoueur $traceJoueur): static
    {
        if ($this->traceJoueurs->removeElement($traceJoueur)) {
            // set the owning side to null (unless already changed)
            if ($traceJoueur->getJoueur() === $this) {
                $traceJoueur->setJoueur(null);
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
            $renouvellement->setJoueur($this);
        }

        return $this;
    }

    public function removeRenouvellement(Renouvellement $renouvellement): static
    {
        if ($this->renouvellements->removeElement($renouvellement)) {
            // set the owning side to null (unless already changed)
            if ($renouvellement->getJoueur() === $this) {
                $renouvellement->setJoueur(null);
            }
        }

        return $this;
    }


}
