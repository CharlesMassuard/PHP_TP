<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'exemplaire est obligatoire.')]
    private ?Exemplaires $exemplaire = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'utilisateur est obligatoire.')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le statut ne peut pas être vide.')]
    #[Assert\Choice(
        choices: ['en_cours', 'retourne', 'en_retard'],
        message: 'Le statut doit être "en_cours", "retourne" ou "en_retard".'
    )]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date d\'emprunt est obligatoire.')]
    #[Assert\LessThanOrEqual(
        value: 'today',
        message: 'La date d\'emprunt ne peut pas être dans le futur.'
    )]
    private ?\DateTimeImmutable $dateEmprunt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date de retour prévue est obligatoire.')]
    #[Assert\GreaterThan(
        propertyPath: 'dateEmprunt',
        message: 'La date de retour doit être après la date d\'emprunt.'
    )]
    private ?\DateTimeImmutable $dateRetour = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(
        propertyPath: 'dateEmprunt',
        message: 'La date de retour effectif ne peut pas être avant la date d\'emprunt.'
    )]
    private ?\DateTime $dateRetourEffectue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExemplaire(): ?Exemplaires
    {
        return $this->exemplaire;
    }

    public function setExemplaire(?Exemplaires $exemplaire): static
    {
        $this->exemplaire = $exemplaire;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getDateEmprunt(): ?\DateTimeImmutable
    {
        return $this->dateEmprunt;
    }

    public function setDateEmprunt(\DateTimeImmutable $dateEmprunt): static
    {
        $this->dateEmprunt = $dateEmprunt;

        return $this;
    }

    public function getDateRetour(): ?\DateTimeImmutable
    {
        return $this->dateRetour;
    }

    public function setDateRetour(\DateTimeImmutable $dateRetour): static
    {
        $this->dateRetour = $dateRetour;

        return $this;
    }

    public function getDateRetourEffectue(): ?\DateTime
    {
        return $this->dateRetourEffectue;
    }

    public function setDateRetourEffectue(?\DateTime $dateRetourEffectue): static
    {
        $this->dateRetourEffectue = $dateRetourEffectue;

        return $this;
    }

    #[Assert\Callback]
    public function validateRetourEffectue(ExecutionContextInterface $context): void
    {
        if ($this->statut === 'retourne' && $this->dateRetourEffectue === null) {
            $context->buildViolation('La date de retour effectif est obligatoire quand le statut est "retourné".')
                ->atPath('dateRetourEffectue')
                ->addViolation();
        }
    }
}