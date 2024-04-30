<?php

namespace App\Entity;

use App\Repository\LigueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigueRepository::class)]
class Ligue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'ligues')]
    private ?Region $region = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    /**
     * @var Collection<int, Club>
     */
    #[ORM\OneToMany(targetEntity: Club::class, mappedBy: 'ligue')]
    private Collection $clubs;

    #[ORM\OneToOne(inversedBy: 'ligue', cascade: ['persist', 'remove'])]
    private ?User $president = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]

    
    public function __toString()
    {
        return $this->nom;
    }
    
    public function __construct()
    {
        $this->clubs = new ArrayCollection();
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Club>
     */
    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClub(Club $club): static
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs->add($club);
            $club->setLigue($this);
        }

        return $this;
    }

    public function removeClub(Club $club): static
    {
        if ($this->clubs->removeElement($club)) {
            // set the owning side to null (unless already changed)
            if ($club->getLigue() === $this) {
                $club->setLigue(null);
            }
        }

        return $this;
    }

    public function getPresident(): ?User
    {
        return $this->president;
    }

    public function setPresident(?User $president): static
    {
        $this->president = $president;

        return $this;
    }

}
