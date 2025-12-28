<?php

namespace App\Tests\Entity;

use App\Entity\Exemplaires;
use App\Entity\EtatExemplaire;
use App\Entity\Ouvrage;
use App\Entity\Emprunt;
use PHPUnit\Framework\TestCase;

class ExemplaireTest extends TestCase
{
    private Exemplaires $exemplaire;

    protected function setUp(): void
    {
        $this->exemplaire = new Exemplaires();
    }

    public function testGetIdReturnsNullByDefault(): void
    {
        $this->assertNull($this->exemplaire->getId());
    }

    public function testSetAndGetCote(): void
    {
        $cote = 'A-123-456';
        $result = $this->exemplaire->setCote($cote);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertSame($cote, $this->exemplaire->getCote());
    }

    public function testSetAndGetEtat(): void
    {
        $etat = EtatExemplaire::MAUVAIS;
        $result = $this->exemplaire->setEtat($etat);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertSame($etat, $this->exemplaire->getEtat());
    }

    public function testSetAndGetEmplacement(): void
    {
        $emplacement = 'Rayon A - Etagère 3';
        $result = $this->exemplaire->setEmplacement($emplacement);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertSame($emplacement, $this->exemplaire->getEmplacement());
    }

    public function testSetAndGetDisponibilite(): void
    {
        $result = $this->exemplaire->setDisponibilite(true);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertTrue($this->exemplaire->getDisponibilite());
        
        $this->exemplaire->setDisponibilite(false);
        $this->assertFalse($this->exemplaire->getDisponibilite());
    }

    public function testSetAndGetOuvrage(): void
    {
        $ouvrage = $this->createMock(Ouvrage::class);
        $result = $this->exemplaire->setOuvrage($ouvrage);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertSame($ouvrage, $this->exemplaire->getOuvrage());
    }

    public function testGetEmpruntsReturnsEmptyCollectionByDefault(): void
    {
        $emprunts = $this->exemplaire->getEmprunts();
        
        $this->assertCount(0, $emprunts);
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $emprunts);
    }

    public function testAddEmprunt(): void
    {
        $emprunt = $this->createMock(Emprunt::class);
        $emprunt->expects($this->once())
            ->method('setExemplaire')
            ->with($this->exemplaire);
        
        $result = $this->exemplaire->addEmprunt($emprunt);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertCount(1, $this->exemplaire->getEmprunts());
        $this->assertTrue($this->exemplaire->getEmprunts()->contains($emprunt));
    }

    public function testAddEmpruntDoesNotDuplicates(): void
    {
        $emprunt = $this->createMock(Emprunt::class);
        $emprunt->expects($this->once())
            ->method('setExemplaire');
        
        $this->exemplaire->addEmprunt($emprunt);
        $this->exemplaire->addEmprunt($emprunt);
        
        $this->assertCount(1, $this->exemplaire->getEmprunts());
    }

    public function testRemoveEmprunt(): void
    {
        $emprunt = $this->createMock(Emprunt::class);
        $emprunt->method('getExemplaire')->willReturn($this->exemplaire);
        $emprunt->expects($this->once())
            ->method('setExemplaire')
            ->with(null);
        
        $this->exemplaire->getEmprunts()->add($emprunt);
        $result = $this->exemplaire->removeEmprunt($emprunt);
        
        $this->assertSame($this->exemplaire, $result);
        $this->assertCount(0, $this->exemplaire->getEmprunts());
    }

    public function testExemplaireComplet(): void
    {
        $ouvrage = $this->createMock(Ouvrage::class);
        $etat = EtatExemplaire::BON;
        
        $this->exemplaire
            ->setCote('B-789')
            ->setEtat($etat)
            ->setEmplacement('Salle de lecture')
            ->setDisponibilite(true)
            ->setOuvrage($ouvrage);

        $this->assertSame('B-789', $this->exemplaire->getCote());
        $this->assertSame(EtatExemplaire::BON, $this->exemplaire->getEtat());
        $this->assertSame('Salle de lecture', $this->exemplaire->getEmplacement());
        $this->assertTrue($this->exemplaire->getDisponibilite());
        $this->assertSame($ouvrage, $this->exemplaire->getOuvrage());
    }
}