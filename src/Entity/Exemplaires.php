<?php

namespace App\Entity;

use App\Repository\ExemplairesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExemplairesRepository::class)]
class Exemplaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Cote = null;

    #[ORM\Column(length: 255)]
    private ?string $Etat = null;

    #[ORM\Column(length: 255)]
    private ?string $Emplacement = null;

    #[ORM\Column(length: 255)]
    private ?string $Disponibilite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCote(): ?string
    {
        return $this->Cote;
    }

    public function setCote(string $Cote): static
    {
        $this->Cote = $Cote;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(string $Etat): static
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->Emplacement;
    }

    public function setEmplacement(string $Emplacement): static
    {
        $this->Emplacement = $Emplacement;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->Disponibilite;
    }

    public function setDisponibilite(string $Disponibilite): static
    {
        $this->Disponibilite = $Disponibilite;

        return $this;
    }
}
