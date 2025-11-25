<?php

namespace App\Entity;

use App\Repository\OuvrageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Exemplaires;

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
    private array $Categories = [];

    #[ORM\Column(type: Types::JSON)]
    private array $Tags = [];

    #[ORM\Column(type: Types::JSON)]
    private array $Langues = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $Annee = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Resume = null;

    #[ORM\OneToMany(mappedBy: 'Ouvrage', targetEntity: Exemplaires::class, cascade: ['persist'], orphanRemoval: false)]
    private Collection $Exemplaires;

    public function __construct()
    {
        $this->Exemplaires = new ArrayCollection();
    }

    /**
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaires(): Collection
    {
        return $this->Exemplaires;
    }

    public function addExemplaire(Exemplaires $exemplaire): static
    {
        if (!$this->Exemplaires->contains($exemplaire)) {
            $this->Exemplaires->add($exemplaire);
            $exemplaire->setOuvrage($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaire): static
    {
        if ($this->Exemplaires->removeElement($exemplaire)) {
            if ($exemplaire->getOuvrage() === $this) {
                $exemplaire->setOuvrage(null);
            }
        }

        return $this;
    }

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

    public function getCategories(): array
    {
        return $this->Categories;
    }

    public function setCategories(array $Categories): static
    {
        $this->Categories = $Categories;

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

    public function getAnnee(): ?\DateTimeImmutable
    {
        return $this->Annee;
    }

    public function setAnnee(\DateTimeImmutable $Annee): static
    {
        $this->Annee = $Annee;

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

    public function getAuteursAsString(): ?string
    {
        return $this->Auteurs ? implode(', ', $this->Auteurs) : '';
    }

    public function setAuteursFromString(?string $auteurs): void
    {
        if ($auteurs) {
            $this->Auteurs = array_map('trim', explode(',', $auteurs));
        } else {
            $this->Auteurs = [];
        }
    }

    public function getLanguesAsString(): ?string
    {
        return $this->Langues ? implode(', ', $this->Langues) : '';
    }

    public function setLanguesFromString(?string $langues): void
    {
        if ($langues) {
            $this->Langues = array_map('trim', explode(',', $langues));
        } else {
            $this->Langues = [];
        }
    }

    public function getCategoriesAsString(): ?string
    {
        return $this->Categories ? implode(', ', $this->Categories) : '';
    }

    public function setCategoriesFromString(?string $categories): void
    {
        if ($categories) {
            $this->Categories = array_map('trim', explode(',', $categories));
        } else {
            $this->Categories = [];
        }
    }

    public function getTagsAsString(): ?string
    {
        return $this->Tags ? implode(', ', $this->Tags) : '';
    }

    public function setTagsFromString(?string $tags): void
    {
        if ($tags) {
            $this->Tags = array_map('trim', explode(',', $tags));
        } else {
            $this->Tags = [];
        }
    }

    #[Assert\Callback]
    public function validateISBNorISSN(ExecutionContextInterface $context): void
    {
        if (empty($this->ISBN) && empty($this->ISSN)) {
            $context->buildViolation('Au moins un des champs ISBN ou ISSN doit être renseigné.')
                ->atPath('ISBN')
                ->addViolation();
        }
    }
}
