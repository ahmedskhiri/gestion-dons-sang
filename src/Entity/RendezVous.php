<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateHeureDebut = null;

    #[ORM\Column]
    private ?\DateTime $dateHeureFin = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Donateur $donateur = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collecte $collecte = null;

    #[ORM\OneToOne(mappedBy: 'rendezVous', cascade: ['persist', 'remove'])]
    private ?Don $don = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTime $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTime
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(\DateTime $dateHeureFin): static
    {
        $this->dateHeureFin = $dateHeureFin;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDonateur(): ?Donateur
    {
        return $this->donateur;
    }

    public function setDonateur(?Donateur $donateur): static
    {
        $this->donateur = $donateur;

        return $this;
    }

    public function getCollecte(): ?Collecte
    {
        return $this->collecte;
    }

    public function setCollecte(?Collecte $collecte): static
    {
        $this->collecte = $collecte;

        return $this;
    }

    public function getDon(): ?Don
    {
        return $this->don;
    }

    public function setDon(?Don $don): static
    {
        // unset the owning side of the relation if necessary
        if ($don === null && $this->don !== null) {
            $this->don->setRendezVous(null);
        }

        // set the owning side of the relation if necessary
        if ($don !== null && $don->getRendezVous() !== $this) {
            $don->setRendezVous($this);
        }

        $this->don = $don;

        return $this;
    }

}
