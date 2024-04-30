<?php

namespace App\Entity;

use App\Repository\AffiliationRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffiliationRepository::class)]
class Affiliation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'affiliations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Club $club = null;

    #[ORM\ManyToOne(inversedBy: 'affiliations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Saison $saison = null;

    #[ORM\Column]
    private ?bool $isAccepted = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $siegeSocialDuClub = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseDuClub = null;

    #[ORM\Column(length: 80)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $fax = null;

    #[ORM\Column(length: 255)]
    private ?string $couleurs = null;

    #[ORM\Column(length: 255)]
    private ?string $mailOfficiel = null;

    #[ORM\Column(length: 255)]
    private ?string $terrains = null;

    #[ORM\Column(length: 255)]
    private ?string $prefecture = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDeDeclation = null;

    #[ORM\Column(length: 255)]
    private ?string $president = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $premiervicePresident = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deuxiemeVidePresident = null;

    #[ORM\Column(length: 255)]
    private ?string $secretaireGeneral = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TresorierGeneral = null;

    #[ORM\OneToMany(mappedBy: 'affiliation', targetEntity: Document::class)]
    private Collection $documents;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->isAccepted = false;
        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id.' '.$this->saison->getNom().' => Président : '.$this->president;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
         $this->id = $id;
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

    public function getSaison(): ?Saison
    {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self
    {
        $this->saison = $saison;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSiegeSocialDuClub(): ?string
    {
        return $this->siegeSocialDuClub;
    }

    public function setSiegeSocialDuClub(string $siegeSocialDuClub): self
    {
        $this->siegeSocialDuClub = $siegeSocialDuClub;

        return $this;
    }

    public function getAdresseDuClub(): ?string
    {
        return $this->adresseDuClub;
    }

    public function setAdresseDuClub(string $adresseDuClub): self
    {
        $this->adresseDuClub = $adresseDuClub;

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

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getCouleurs(): ?string
    {
        return $this->couleurs;
    }

    public function setCouleurs(string $couleurs): self
    {
        $this->couleurs = $couleurs;

        return $this;
    }

    public function getMailOfficiel(): ?string
    {
        return $this->mailOfficiel;
    }

    public function setMailOfficiel(string $mailOfficiel): self
    {
        $this->mailOfficiel = $mailOfficiel;

        return $this;
    }

    public function getTerrains(): ?string
    {
        return $this->terrains;
    }

    public function setTerrains(string $terrains): self
    {
        $this->terrains = $terrains;

        return $this;
    }

    public function getPrefecture(): ?string
    {
        return $this->prefecture;
    }

    public function setPrefecture(string $prefecture): self
    {
        $this->prefecture = $prefecture;

        return $this;
    }

    public function getDateDeDeclation(): ?\DateTimeInterface
    {
        return $this->dateDeDeclation;
    }

    public function setDateDeDeclation(\DateTimeInterface $dateDeDeclation): self
    {
        $this->dateDeDeclation = $dateDeDeclation;

        return $this;
    }

    public function getPresident(): ?string
    {
        return $this->president;
    }

    public function setPresident(string $president): self
    {
        $this->president = $president;

        return $this;
    }

    public function getPremiervicePresident(): ?string
    {
        return $this->premiervicePresident;
    }

    public function setPremiervicePresident(?string $premiervicePresident): self
    {
        $this->premiervicePresident = $premiervicePresident;

        return $this;
    }

    public function getDeuxiemeVidePresident(): ?string
    {
        return $this->deuxiemeVidePresident;
    }

    public function setDeuxiemeVidePresident(?string $deuxiemeVidePresident): self
    {
        $this->deuxiemeVidePresident = $deuxiemeVidePresident;

        return $this;
    }

    public function getSecretaireGeneral(): ?string
    {
        return $this->secretaireGeneral;
    }

    public function setSecretaireGeneral(string $secretaireGeneral): self
    {
        $this->secretaireGeneral = $secretaireGeneral;

        return $this;
    }

    public function getTresorierGeneral(): ?string
    {
        return $this->TresorierGeneral;
    }

    public function setTresorierGeneral(?string $TresorierGeneral): self
    {
        $this->TresorierGeneral = $TresorierGeneral;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setAffiliation($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getAffiliation() === $this) {
                $document->setAffiliation(null);
            }
        }

        return $this;
    }
}
