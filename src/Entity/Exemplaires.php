<?php

namespace App\Entity;

use App\Repository\ExemplairesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Entity\Ouvrage;

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

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $Disponibilite = null;

    #[ORM\ManyToOne(targetEntity: Ouvrage::class, inversedBy: 'Exemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ouvrage $Ouvrage = null;

    public function getOuvrage(): ?Ouvrage
    {
        return $this->Ouvrage;
    }

    public function setOuvrage(?Ouvrage $ouvrage): static
    {
        $this->Ouvrage = $ouvrage;
        return $this;
    }

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

    public function getDisponibilite(): ?bool
    {
        return $this->Disponibilite;
    }

    public function setDisponibilite(bool $Disponibilite): static
    {
        $this->Disponibilite = $Disponibilite;

        return $this;
    }
}
