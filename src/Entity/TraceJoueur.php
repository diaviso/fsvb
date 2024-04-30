<?php

namespace App\Entity;

use App\Repository\TraceJoueurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraceJoueurRepository::class)]
class TraceJoueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'traceJoueurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Joueur $joueur = null;

    #[ORM\ManyToOne(inversedBy: 'traceJoueurs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSave = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJoueur(): ?Joueur
    {
        return $this->joueur;
    }

    public function setJoueur(?Joueur $joueur): static
    {
        $this->joueur = $joueur;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->dateSave;
    }

    public function setDate(\DateTimeInterface $dateSave): static
    {
        $this->dateSave = $dateSave;

        return $this;
    }
}
