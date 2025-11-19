<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonRepository::class)]
class Don
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDon = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 50)]
    private ?string $typeDon = null;

    #[ORM\Column]
    private ?bool $apte = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Donateur $donateur = null;

    #[ORM\OneToOne(inversedBy: 'don', cascade: ['persist', 'remove'])]
    private ?RendezVous $rendezVous = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDon(): ?\DateTime
    {
        return $this->dateDon;
    }

    public function setDateDon(\DateTime $dateDon): static
    {
        $this->dateDon = $dateDon;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getTypeDon(): ?string
    {
        return $this->typeDon;
    }

    public function setTypeDon(string $typeDon): static
    {
        $this->typeDon = $typeDon;

        return $this;
    }

    public function isApte(): ?bool
    {
        return $this->apte;
    }

    public function setApte(bool $apte): static
    {
        $this->apte = $apte;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

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

    public function getRendezVous(): ?RendezVous
    {
        return $this->rendezVous;
    }

    public function setRendezVous(?RendezVous $rendezVous): static
    {
        $this->rendezVous = $rendezVous;

        return $this;
    }

    
    


    
}
