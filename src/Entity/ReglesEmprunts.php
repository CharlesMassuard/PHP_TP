<?php

namespace App\Entity;

use App\Repository\ReglesEmpruntsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReglesEmpruntsRepository::class)]
class ReglesEmprunts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\Column]
    private ?int $dureeEmpruntJours = null;

    #[ORM\Column]
    private ?int $nombreMaxEmrpunts = null;

    #[ORM\Column]
    private ?float $penaliteParJour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDureeEmpruntJours(): ?int
    {
        return $this->dureeEmpruntJours;
    }

    public function setDureeEmpruntJours(int $dureeEmpruntJours): static
    {
        $this->dureeEmpruntJours = $dureeEmpruntJours;

        return $this;
    }

    public function getNombreMaxEmrpunts(): ?int
    {
        return $this->nombreMaxEmrpunts;
    }

    public function setNombreMaxEmrpunts(int $nombreMaxEmrpunts): static
    {
        $this->nombreMaxEmrpunts = $nombreMaxEmrpunts;

        return $this;
    }

    public function getPenaliteParJour(): ?float
    {
        return $this->penaliteParJour;
    }

    public function setPenaliteParJour(float $penaliteParJour): static
    {
        $this->penaliteParJour = $penaliteParJour;

        return $this;
    }
}
