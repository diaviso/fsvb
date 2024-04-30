<?php

namespace App\Entity;

use App\Repository\SaisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaisonRepository::class)]
class Saison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $dateDeDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $dateDeFin = null;

    #[ORM\Column]
    private ?bool $isOpen = null;

    #[ORM\OneToMany(mappedBy: 'saison', targetEntity: Affiliation::class)]
    private Collection $affiliations;

    /**
     * @var Collection<int, Renouvellement>
     */
    #[ORM\OneToMany(targetEntity: Renouvellement::class, mappedBy: 'saison')]
    private Collection $renouvellements;

    public function __toString()
    {
        return $this->getNom();
    }
    
    public function __construct()
    {
        $this->affiliations = new ArrayCollection();
        $this->isOpen = true;
        $this->renouvellements = new ArrayCollection();
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

    public function getDateDeDebut(): ?\DateTime
    {
        return $this->dateDeDebut;
    }

    public function setDateDeDebut(?\DateTime $dateDeDebut): static
    {
        $this->dateDeDebut = $dateDeDebut;

        return $this;
    }

    public function getDateDeFin(): ?\DateTime
    {
        return $this->dateDeFin;
    }

    public function setDateDeFin(?\DateTime $dateDeFin): static
    {
        $this->dateDeFin = $dateDeFin;

        return $this;
    }

    public function isOpen(): ?bool
    {
        return $this->isOpen;
    }

    public function setOpen(bool $isOpen): static
    {
        $this->isOpen = $isOpen;

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
            $affiliation->setSaison($this);
        }

        return $this;
    }

    public function removeAffiliation(Affiliation $affiliation): self
    {
        if ($this->affiliations->removeElement($affiliation)) {
            // set the owning side to null (unless already changed)
            if ($affiliation->getSaison() === $this) {
                $affiliation->setSaison(null);
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
            $renouvellement->setSaison($this);
        }

        return $this;
    }

    public function removeRenouvellement(Renouvellement $renouvellement): static
    {
        if ($this->renouvellements->removeElement($renouvellement)) {
            // set the owning side to null (unless already changed)
            if ($renouvellement->getSaison() === $this) {
                $renouvellement->setSaison(null);
            }
        }

        return $this;
    }
}
