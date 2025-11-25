<?php

namespace App\Entity;

use App\Repository\AuditLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $actionEffectuee = null;

    #[ORM\Column(type: Types::JSON)]
    private array $details = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function __construct(
        string $actionEffectuee,
        array $details = [],
        ?User $user = null
    ) {
        $this->actionEffectuee = $actionEffectuee;
        $this->details = $details;
        $this->user = $user;
        $this->date = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActionEffectuee(): ?string
    {
        return $this->actionEffectuee;
    }

    public function setActionEffectuee(string $actionEffectuee): static
    {
        $this->actionEffectuee = $actionEffectuee;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

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
}
