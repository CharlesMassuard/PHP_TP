<?php

namespace App\Entity;

use App\Repository\ReglesEmpruntsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReglesEmpruntsRepository::class)]
class ReglesEmprunts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La catégorie est obligatoire.')]
    #[Assert\Length(max: 255, maxMessage: 'La catégorie ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $categorie = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La durée d’emprunt est obligatoire.')]
    #[Assert\Positive(message: 'La durée d’emprunt doit être un entier strictement positif.')]
    private ?int $dureeEmpruntJours = null;

    // Conserve le nom de colonne existant (typo) pour éviter une migration.
    #[ORM\Column(name: 'nombre_max_emrpunts')]
    #[Assert\NotNull(message: 'Le nombre maximal d’emprunts est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le nombre maximal d’emprunts doit être ≥ 0.')]
    private ?int $nombreMaxEmprunts = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La pénalité par jour est obligatoire.')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La pénalité par jour doit être ≥ 0.')]
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

    public function getNombreMaxEmprunts(): ?int
    {
        return $this->nombreMaxEmprunts;
    }

    public function setNombreMaxEmprunts(int $nombreMaxEmprunts): static
    {
        $this->nombreMaxEmprunts = $nombreMaxEmprunts;
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