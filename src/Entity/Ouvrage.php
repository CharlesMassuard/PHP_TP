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
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide.')]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractère.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank(message: 'Au moins un auteur doit être renseigné.')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Au moins un auteur doit être renseigné.'
    )]
    private array $auteurs = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'éditeur ne peut pas être vide.')]
    #[Assert\Length(max: 255)]
    private ?string $editeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Isbn(
        message: 'Le format ISBN est invalide.'
    )]
    private ?string $isbn = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Issn(
        message: 'Le format ISSN est invalide.'
    )]
    private ?string $issn = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank(message: 'Au moins une catégorie doit être renseignée.')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Au moins une catégorie doit être renseignée.'
    )]
    private array $categories = [];

    #[ORM\Column(type: Types::JSON)]
    private array $tags = [];

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank(message: 'Au moins une langue doit être renseignée.')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Au moins une langue doit être renseignée.'
    )]
    private array $langues = [];

    #[ORM\Column]
    #[Assert\NotNull(message: 'L\'année de parution est obligatoire.')]
    #[Assert\LessThanOrEqual(
        value: 'today',
        message: 'L\'année ne peut pas être dans le futur.'
    )]
    private ?\DateTimeImmutable $annee = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le résumé ne peut pas être vide.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Le résumé doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $resume = null;

    #[ORM\OneToMany(mappedBy: 'ouvrage', targetEntity: Exemplaires::class, cascade: ['persist'], orphanRemoval: false)]
    private Collection $exemplaires;

    public function __construct()
    {
        $this->exemplaires = new ArrayCollection();
    }

    /**
     * @return Collection<int, Exemplaires>
     */
    public function getExemplaires(): Collection
    {
        return $this->exemplaires;
    }

    public function addExemplaire(Exemplaires $exemplaire): static
    {
        if (!$this->exemplaires->contains($exemplaire)) {
            $this->exemplaires->add($exemplaire);
            $exemplaire->setOuvrage($this);
        }

        return $this;
    }

    public function removeExemplaire(Exemplaires $exemplaire): static
    {
        if ($this->exemplaires->removeElement($exemplaire)) {
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
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteurs(): array
    {
        return $this->auteurs;
    }

    public function setAuteurs(array $auteurs): static
    {
        $this->auteurs = $auteurs;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(string $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->isbn;
    }

    public function setISBN(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getISSN(): ?string
    {
        return $this->issn;
    }

    public function setISSN(?string $issn): static
    {
        $this->issn = $issn;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getLangues(): array
    {
        return $this->langues;
    }

    public function setLangues(array $langues): static
    {
        $this->langues = $langues;

        return $this;
    }

    public function getAnnee(): ?\DateTimeImmutable
    {
        return $this->annee;
    }

    public function setAnnee(\DateTimeImmutable $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getAuteursAsString(): ?string
    {
        return $this->auteurs ? implode(', ', $this->auteurs) : '';
    }

    public function setAuteursFromString(?string $auteurs): void
    {
        if ($auteurs) {
            $this->auteurs = array_map('trim', explode(',', $auteurs));
        } else {
            $this->auteurs = [];
        }
    }

    public function getLanguesAsString(): ?string
    {
        return $this->langues ? implode(', ', $this->langues) : '';
    }

    public function setLanguesFromString(?string $langues): void
    {
        if ($langues) {
            $this->langues = array_map('trim', explode(',', $langues));
        } else {
            $this->langues = [];
        }
    }

    public function getCategoriesAsString(): ?string
    {
        return $this->categories ? implode(', ', $this->categories) : '';
    }

    public function setCategoriesFromString(?string $categories): void
    {
        if ($categories) {
            $this->categories = array_map('trim', explode(',', $categories));
        } else {
            $this->categories = [];
        }
    }

    public function getTagsAsString(): ?string
    {
        return $this->tags ? implode(', ', $this->tags) : '';
    }

    public function setTagsFromString(?string $tags): void
    {
        if ($tags) {
            $this->tags = array_map('trim', explode(',', $tags));
        } else {
            $this->tags = [];
        }
    }

    #[Assert\Callback]
    public function validateISBNorISSN(ExecutionContextInterface $context): void
    {
        if (empty($this->isbn) && empty($this->issn)) {
            $context->buildViolation('Au moins un des champs ISBN ou ISSN doit être renseigné.')
                ->atPath('ISBN')
                ->addViolation();
        }
    }
}