<?php

namespace App\Entity;

use App\Repository\ExemplairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Entity\Ouvrage;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

enum EtatExemplaire: string
{
    case BON = 'bon';
    case MOYEN = 'moyen';
    case MAUVAIS = 'mauvais';
}

#[ORM\Entity(repositoryClass: ExemplairesRepository::class)]
class Exemplaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La cote est obligatoire.')]
    #[Assert\Length(max: 255, maxMessage: 'La cote ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $cote = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'L’état de l’exemplaire est obligatoire.')]
    #[Assert\Choice(choices: [EtatExemplaire::BON, EtatExemplaire::MOYEN, EtatExemplaire::MAUVAIS], message: 'État invalide.')]
    private ?EtatExemplaire $etat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L’emplacement est obligatoire.')]
    #[Assert\Length(max: 255, maxMessage: 'L’emplacement ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $emplacement = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull(message: 'La disponibilité est obligatoire.')]
    private ?bool $disponibilite = null;

    #[ORM\ManyToOne(targetEntity: Ouvrage::class, inversedBy: 'exemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L’ouvrage associé est obligatoire.')]
    private ?Ouvrage $ouvrage = null;

    /**
     * @var Collection<int, Emprunt>
     */
    #[ORM\OneToMany(targetEntity: Emprunt::class, mappedBy: 'exemplaire')]
    private Collection $emprunts;

    public function __construct()
    {
        $this->emprunts = new ArrayCollection();
    }

    public function getOuvrage(): ?Ouvrage
    {
        return $this->ouvrage;
    }

    public function setOuvrage(?Ouvrage $ouvrage): static
    {
        $this->ouvrage = $ouvrage;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCote(): ?string
    {
        return $this->cote;
    }

    public function setCote(string $cote): static
    {
        $this->cote = $cote;

        return $this;
    }

    public function getEtat(): ?EtatExemplaire
    {
        return $this->etat;
    }

    public function setEtat(EtatExemplaire $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    /**
     * @return Collection<int, Emprunt>
     */
    public function getEmprunts(): Collection
    {
        return $this->emprunts;
    }

    public function addEmprunt(Emprunt $emprunt): static
    {
        if (!$this->emprunts->contains($emprunt)) {
            $this->emprunts->add($emprunt);
            $emprunt->setExemplaire($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunt $emprunt): static
    {
        if ($this->emprunts->removeElement($emprunt)) {
            if ($emprunt->getExemplaire() === $this) {
                $emprunt->setExemplaire(null);
            }
        }

        return $this;
    }

    #[Assert\Callback]
    public function validateDisponibilite(ExecutionContextInterface $context): void
    {
        // Si un emprunt "en_cours" existe, la disponibilité ne doit pas être true
        foreach ($this->emprunts as $emprunt) {
            if ($emprunt->getStatut() === 'en_cours' && $this->disponibilite === true) {
                $context->buildViolation('Un exemplaire emprunté ne peut pas être marqué disponible.')
                    ->atPath('disponibilite')
                    ->addViolation();
                break;
            }
        }
    }
}