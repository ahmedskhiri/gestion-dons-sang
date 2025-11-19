<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $groupeSanguin = null;

    #[ORM\Column]
    private ?int $niveauActuel = null;

    #[ORM\Column]
    private ?\DateTime $derniereMiseAJour = null;

    #[ORM\Column]
    private ?int $niveauAlerte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupeSanguin(): ?string
    {
        return $this->groupeSanguin;
    }

    public function setGroupeSanguin(string $groupeSanguin): static
    {
        $this->groupeSanguin = $groupeSanguin;

        return $this;
    }

    public function getNiveauActuel(): ?int
    {
        return $this->niveauActuel;
    }

    public function setNiveauActuel(int $niveauActuel): static
    {
        $this->niveauActuel = $niveauActuel;

        return $this;
    }

    public function getDerniereMiseAJour(): ?\DateTime
    {
        return $this->derniereMiseAJour;
    }

    public function setDerniereMiseAJour(\DateTime $derniereMiseAJour): static
    {
        $this->derniereMiseAJour = $derniereMiseAJour;

        return $this;
    }

    public function getNiveauAlerte(): ?int
    {
        return $this->niveauAlerte;
    }

    public function setNiveauAlerte(int $niveauAlerte): static
    {
        $this->niveauAlerte = $niveauAlerte;

        return $this;
    }
}
