<?php

namespace App\Entity;

use App\Repository\OuvrageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OuvrageRepository::class)]
class Ouvrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Titre = null;

    #[ORM\Column(type: Types::JSON)]
    private array $Auteurs = [];

    #[ORM\Column(length: 255)]
    private ?string $Editeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ISBN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ISSN = null;

    #[ORM\Column(type: Types::JSON)]
    private array $Catégories = [];

    #[ORM\Column(type: Types::JSON)]
    private array $Tags = [];

    #[ORM\Column(type: Types::JSON)]
    private array $Langues = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $Année = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Resume = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): static
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getAuteurs(): array
    {
        return $this->Auteurs;
    }

    public function setAuteurs(array $Auteurs): static
    {
        $this->Auteurs = $Auteurs;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->Editeur;
    }

    public function setEditeur(string $Editeur): static
    {
        $this->Editeur = $Editeur;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(?string $ISBN): static
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    public function getISSN(): ?string
    {
        return $this->ISSN;
    }

    public function setISSN(?string $ISSN): static
    {
        $this->ISSN = $ISSN;

        return $this;
    }

    public function getCatégories(): array
    {
        return $this->Catégories;
    }

    public function setCatégories(array $Catégories): static
    {
        $this->Catégories = $Catégories;

        return $this;
    }

    public function getTags(): array
    {
        return $this->Tags;
    }

    public function setTags(array $Tags): static
    {
        $this->Tags = $Tags;

        return $this;
    }

    public function getLangues(): array
    {
        return $this->Langues;
    }

    public function setLangues(array $Langues): static
    {
        $this->Langues = $Langues;

        return $this;
    }

    public function getAnnée(): ?\DateTimeImmutable
    {
        return $this->Année;
    }

    public function setAnnée(\DateTimeImmutable $Année): static
    {
        $this->Année = $Année;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->Resume;
    }

    public function setResume(string $Resume): static
    {
        $this->Resume = $Resume;

        return $this;
    }
}
