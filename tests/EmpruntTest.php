<?php

namespace App\Tests\Entity;

use App\Entity\Emprunt;
use App\Entity\Exemplaires;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EmpruntTest extends TestCase
{
    private Emprunt $emprunt;

    protected function setUp(): void
    {
        $this->emprunt = new Emprunt();
    }

    public function testGetIdReturnsNullByDefault(): void
    {
        $this->assertNull($this->emprunt->getId());
    }

    public function testSetAndGetExemplaire(): void
    {
        $exemplaire = $this->createMock(Exemplaires::class);
        
        $result = $this->emprunt->setExemplaire($exemplaire);
        
        $this->assertSame($this->emprunt, $result);
        $this->assertSame($exemplaire, $this->emprunt->getExemplaire());
    }

    public function testGetExemplaireReturnsNullByDefault(): void
    {
        $this->assertNull($this->emprunt->getExemplaire());
    }

    public function testSetAndGetUser(): void
    {
        $user = $this->createMock(User::class);
        
        $result = $this->emprunt->setUser($user);
        
        $this->assertSame($this->emprunt, $result);
        $this->assertSame($user, $this->emprunt->getUser());
    }

    public function testGetUserReturnsNullByDefault(): void
    {
        $this->assertNull($this->emprunt->getUser());
    }

    public function testSetAndGetStatut(): void
    {
        $statut = 'en_cours';
        
        $result = $this->emprunt->setStatut($statut);
        
        $this->assertSame($this->emprunt, $result);
        $this->assertSame($statut, $this->emprunt->getStatut());
    }

    public function testGetStatutReturnsNullByDefault(): void
    {
        $this->assertNull($this->emprunt->getStatut());
    }

    public function testSetAndGetDateRetour(): void
    {
        $date = new \DateTimeImmutable('2025-12-31');
        
        $result = $this->emprunt->setDateRetour($date);
        
        $this->assertSame($this->emprunt, $result);
        $this->assertSame($date, $this->emprunt->getDateRetour());
    }

    public function testGetDateRetourReturnsNullByDefault(): void
    {
        $this->assertNull($this->emprunt->getDateRetour());
    }

    public function testEmpruntComplet(): void
    {
        $exemplaire = $this->createMock(Exemplaires::class);
        $user = $this->createMock(User::class);
        $statut = 'termine';
        $dateRetour = new \DateTimeImmutable('2025-06-15');

        $this->emprunt
            ->setExemplaire($exemplaire)
            ->setUser($user)
            ->setStatut($statut)
            ->setDateRetour($dateRetour);

        $this->assertSame($exemplaire, $this->emprunt->getExemplaire());
        $this->assertSame($user, $this->emprunt->getUser());
        $this->assertSame($statut, $this->emprunt->getStatut());
        $this->assertSame($dateRetour, $this->emprunt->getDateRetour());
    }

    public function testFluentInterface(): void
    {
        $exemplaire = $this->createMock(Exemplaires::class);
        $user = $this->createMock(User::class);

        $result = $this->emprunt
            ->setExemplaire($exemplaire)
            ->setUser($user)
            ->setStatut('en_retard')
            ->setDateRetour(new \DateTimeImmutable());

        $this->assertInstanceOf(Emprunt::class, $result);
        $this->assertSame($this->emprunt, $result);
    }
}