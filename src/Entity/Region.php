<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Ligue>
     */
    #[ORM\OneToMany(targetEntity: Ligue::class, mappedBy: 'region')]
    private Collection $ligues;

    public function __toString()
    {
        return $this->nom;
    }

    public function __construct()
    {
        $this->ligues = new ArrayCollection();
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

    /**
     * @return Collection<int, Ligue>
     */
    public function getLigues(): Collection
    {
        return $this->ligues;
    }

    public function addLigue(Ligue $ligue): static
    {
        if (!$this->ligues->contains($ligue)) {
            $this->ligues->add($ligue);
            $ligue->setRegion($this);
        }

        return $this;
    }

    public function removeLigue(Ligue $ligue): static
    {
        if ($this->ligues->removeElement($ligue)) {
            // set the owning side to null (unless already changed)
            if ($ligue->getRegion() === $this) {
                $ligue->setRegion(null);
            }
        }

        return $this;
    }
}
