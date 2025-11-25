<?php

namespace App\Tests\Entity;

use App\Entity\ReglesEmprunts;
use PHPUnit\Framework\TestCase;

class ReglesEmpruntTest extends TestCase
{
    private ReglesEmprunts $regles;

    protected function setUp(): void
    {
        $this->regles = new ReglesEmprunts();
    }

    public function testGetIdReturnsNullByDefault(): void
    {
        $this->assertNull($this->regles->getId());
    }

    public function testSetAndGetCategorie(): void
    {
        $categorie = 'Etudiant';
        $result = $this->regles->setCategorie($categorie);
        
        $this->assertSame($this->regles, $result);
        $this->assertSame($categorie, $this->regles->getCategorie());
    }

    public function testSetAndGetDureeEmpruntJours(): void
    {
        $duree = 14;
        $result = $this->regles->setDureeEmpruntJours($duree);
        
        $this->assertSame($this->regles, $result);
        $this->assertSame($duree, $this->regles->getDureeEmpruntJours());
    }

    public function testSetAndGetNombreMaxEmrpunts(): void
    {
        $nombre = 5;
        $result = $this->regles->setNombreMaxEmrpunts($nombre);
        
        $this->assertSame($this->regles, $result);
        $this->assertSame($nombre, $this->regles->getNombreMaxEmrpunts());
    }

    public function testSetAndGetPenaliteParJour(): void
    {
        $penalite = 0.50;
        $result = $this->regles->setPenaliteParJour($penalite);
        
        $this->assertSame($this->regles, $result);
        $this->assertSame($penalite, $this->regles->getPenaliteParJour());
    }

    public function testGetCategorieReturnsNullByDefault(): void
    {
        $this->assertNull($this->regles->getCategorie());
    }

    public function testGetDureeEmpruntJoursReturnsNullByDefault(): void
    {
        $this->assertNull($this->regles->getDureeEmpruntJours());
    }

    public function testGetNombreMaxEmrpuntsReturnsNullByDefault(): void
    {
        $this->assertNull($this->regles->getNombreMaxEmrpunts());
    }

    public function testGetPenaliteParJourReturnsNullByDefault(): void
    {
        $this->assertNull($this->regles->getPenaliteParJour());
    }

    public function testReglesEmpruntsComplet(): void
    {
        $this->regles
            ->setCategorie('Professeur')
            ->setDureeEmpruntJours(30)
            ->setNombreMaxEmrpunts(10)
            ->setPenaliteParJour(0.25);

        $this->assertSame('Professeur', $this->regles->getCategorie());
        $this->assertSame(30, $this->regles->getDureeEmpruntJours());
        $this->assertSame(10, $this->regles->getNombreMaxEmrpunts());
        $this->assertSame(0.25, $this->regles->getPenaliteParJour());
    }

    public function testFluentInterface(): void
    {
        $result = $this->regles
            ->setCategorie('Etudiant')
            ->setDureeEmpruntJours(14)
            ->setNombreMaxEmrpunts(3)
            ->setPenaliteParJour(0.50);

        $this->assertInstanceOf(ReglesEmprunts::class, $result);
        $this->assertSame($this->regles, $result);
    }

    public function testDifferentesCategories(): void
    {
        $categories = ['Etudiant', 'Professeur', 'Personnel', 'Externe'];
        
        foreach ($categories as $categorie) {
            $this->regles->setCategorie($categorie);
            $this->assertSame($categorie, $this->regles->getCategorie());
        }
    }

    public function testPenaliteAvecDecimales(): void
    {
        $penalites = [0.25, 0.50, 0.75, 1.00, 1.50];
        
        foreach ($penalites as $penalite) {
            $this->regles->setPenaliteParJour($penalite);
            $this->assertSame($penalite, $this->regles->getPenaliteParJour());
        }
    }
}