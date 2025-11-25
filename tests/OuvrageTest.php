<?php

namespace App\Tests\Entity;

use App\Entity\Ouvrage;
use App\Entity\Exemplaires;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class OuvrageTest extends TestCase
{
    private Ouvrage $ouvrage;

    protected function setUp(): void
    {
        $this->ouvrage = new Ouvrage();
    }

    public function testGetIdReturnsNullByDefault(): void
    {
        $this->assertNull($this->ouvrage->getId());
    }

    public function testSetAndGetTitre(): void
    {
        $titre = 'Les Misérables';
        $result = $this->ouvrage->setTitre($titre);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($titre, $this->ouvrage->getTitre());
    }

    public function testSetAndGetAuteurs(): void
    {
        $auteurs = ['Victor Hugo', 'Alexandre Dumas'];
        $result = $this->ouvrage->setAuteurs($auteurs);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($auteurs, $this->ouvrage->getAuteurs());
    }

    public function testSetAndGetEditeur(): void
    {
        $editeur = 'Gallimard';
        $result = $this->ouvrage->setEditeur($editeur);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($editeur, $this->ouvrage->getEditeur());
    }

    public function testSetAndGetISBN(): void
    {
        $isbn = '978-2-07-036822-8';
        $result = $this->ouvrage->setISBN($isbn);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($isbn, $this->ouvrage->getISBN());
    }

    public function testSetAndGetISSN(): void
    {
        $issn = '1234-5678';
        $result = $this->ouvrage->setISSN($issn);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($issn, $this->ouvrage->getISSN());
    }

    public function testSetAndGetCategories(): void
    {
        $categories = ['Fiction', 'Classique'];
        $result = $this->ouvrage->setCategories($categories);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($categories, $this->ouvrage->getCategories());
    }

    public function testSetAndGetTags(): void
    {
        $tags = ['19ème siècle', 'Roman historique'];
        $result = $this->ouvrage->setTags($tags);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($tags, $this->ouvrage->getTags());
    }

    public function testSetAndGetLangues(): void
    {
        $langues = ['Français', 'Anglais'];
        $result = $this->ouvrage->setLangues($langues);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($langues, $this->ouvrage->getLangues());
    }

    public function testSetAndGetAnnee(): void
    {
        $annee = new \DateTimeImmutable('2020-01-01');
        $result = $this->ouvrage->setAnnee($annee);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($annee, $this->ouvrage->getAnnee());
    }

    public function testSetAndGetResume(): void
    {
        $resume = 'Un grand roman sur la rédemption et la justice sociale.';
        $result = $this->ouvrage->setResume($resume);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertSame($resume, $this->ouvrage->getResume());
    }

    public function testGetExemplairesReturnsEmptyCollectionByDefault(): void
    {
        $exemplaires = $this->ouvrage->getExemplaires();
        
        $this->assertCount(0, $exemplaires);
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $exemplaires);
    }

    public function testAddExemplaire(): void
    {
        $exemplaire = $this->createMock(Exemplaires::class);
        $exemplaire->expects($this->once())
            ->method('setOuvrage')
            ->with($this->ouvrage);
        
        $result = $this->ouvrage->addExemplaire($exemplaire);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertCount(1, $this->ouvrage->getExemplaires());
        $this->assertTrue($this->ouvrage->getExemplaires()->contains($exemplaire));
    }

    public function testAddExemplaireDoesNotDuplicates(): void
    {
        $exemplaire = $this->createMock(Exemplaires::class);
        $exemplaire->expects($this->once())
            ->method('setOuvrage');
        
        $this->ouvrage->addExemplaire($exemplaire);
        $this->ouvrage->addExemplaire($exemplaire);
        
        $this->assertCount(1, $this->ouvrage->getExemplaires());
    }

    public function testRemoveExemplaire(): void
    {
        $exemplaire = $this->createMock(Exemplaires::class);
        $exemplaire->method('getOuvrage')->willReturn($this->ouvrage);
        $exemplaire->expects($this->once())
            ->method('setOuvrage')
            ->with(null);
        
        $this->ouvrage->getExemplaires()->add($exemplaire);
        $result = $this->ouvrage->removeExemplaire($exemplaire);
        
        $this->assertSame($this->ouvrage, $result);
        $this->assertCount(0, $this->ouvrage->getExemplaires());
    }

    public function testGetAuteursAsString(): void
    {
        $this->ouvrage->setAuteurs(['Victor Hugo', 'Alexandre Dumas']);
        
        $this->assertSame('Victor Hugo, Alexandre Dumas', $this->ouvrage->getAuteursAsString());
    }

    public function testGetAuteursAsStringReturnsEmptyStringWhenEmpty(): void
    {
        $this->ouvrage->setAuteurs([]);
        
        $this->assertSame('', $this->ouvrage->getAuteursAsString());
    }

    public function testSetAuteursFromString(): void
    {
        $this->ouvrage->setAuteursFromString('Victor Hugo, Alexandre Dumas');
        
        $this->assertSame(['Victor Hugo', 'Alexandre Dumas'], $this->ouvrage->getAuteurs());
    }

    public function testSetAuteursFromStringTrimsSpaces(): void
    {
        $this->ouvrage->setAuteursFromString('  Victor Hugo  ,  Alexandre Dumas  ');
        
        $this->assertSame(['Victor Hugo', 'Alexandre Dumas'], $this->ouvrage->getAuteurs());
    }

    public function testSetAuteursFromStringWithNull(): void
    {
        $this->ouvrage->setAuteursFromString(null);
        
        $this->assertSame([], $this->ouvrage->getAuteurs());
    }

    public function testGetLanguesAsString(): void
    {
        $this->ouvrage->setLangues(['Français', 'Anglais']);
        
        $this->assertSame('Français, Anglais', $this->ouvrage->getLanguesAsString());
    }

    public function testSetLanguesFromString(): void
    {
        $this->ouvrage->setLanguesFromString('Français, Anglais');
        
        $this->assertSame(['Français', 'Anglais'], $this->ouvrage->getLangues());
    }

    public function testGetCategoriesAsString(): void
    {
        $this->ouvrage->setCategories(['Fiction', 'Classique']);
        
        $this->assertSame('Fiction, Classique', $this->ouvrage->getCategoriesAsString());
    }

    public function testSetCategoriesFromString(): void
    {
        $this->ouvrage->setCategoriesFromString('Fiction, Classique');
        
        $this->assertSame(['Fiction', 'Classique'], $this->ouvrage->getCategories());
    }

    public function testGetTagsAsString(): void
    {
        $this->ouvrage->setTags(['19ème siècle', 'Roman historique']);
        
        $this->assertSame('19ème siècle, Roman historique', $this->ouvrage->getTagsAsString());
    }

    public function testSetTagsFromString(): void
    {
        $this->ouvrage->setTagsFromString('19ème siècle, Roman historique');
        
        $this->assertSame(['19ème siècle', 'Roman historique'], $this->ouvrage->getTags());
    }

    public function testValidateISBNorISSNAddsViolationWhenBothEmpty(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('Au moins un des champs ISBN ou ISSN doit être renseigné.')
            ->willReturn($builder);
        
        $builder->expects($this->once())
            ->method('atPath')
            ->with('ISBN')
            ->willReturn($builder);
        
        $builder->expects($this->once())
            ->method('addViolation');
        
        $this->ouvrage->validateISBNorISSN($context);
    }

    public function testValidateISBNorISSNDoesNotAddViolationWhenISBNSet(): void
    {
        $this->ouvrage->setISBN('978-2-07-036822-8');
        
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
            ->method('buildViolation');
        
        $this->ouvrage->validateISBNorISSN($context);
    }

    public function testValidateISBNorISSNDoesNotAddViolationWhenISSNSet(): void
    {
        $this->ouvrage->setISSN('1234-5678');
        
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
            ->method('buildViolation');
        
        $this->ouvrage->validateISBNorISSN($context);
    }

    public function testOuvrageComplet(): void
    {
        $annee = new \DateTimeImmutable('2020-01-01');
        
        $this->ouvrage
            ->setTitre('Les Misérables')
            ->setAuteurs(['Victor Hugo'])
            ->setEditeur('Gallimard')
            ->setISBN('978-2-07-036822-8')
            ->setCategories(['Fiction'])
            ->setTags(['19ème siècle'])
            ->setLangues(['Français'])
            ->setAnnee($annee)
            ->setResume('Un chef-d\'œuvre de la littérature française');

        $this->assertSame('Les Misérables', $this->ouvrage->getTitre());
        $this->assertSame(['Victor Hugo'], $this->ouvrage->getAuteurs());
        $this->assertSame('Gallimard', $this->ouvrage->getEditeur());
        $this->assertSame('978-2-07-036822-8', $this->ouvrage->getISBN());
    }
}