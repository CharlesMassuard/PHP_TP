<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117173148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, auteurs, editeur, isbn, issn, categories, tags, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, auteurs CLOB NOT NULL --(DC2Type:json)
        , editeur VARCHAR(255) NOT NULL, isbn VARCHAR(255) DEFAULT NULL, issn VARCHAR(255) DEFAULT NULL, categories CLOB NOT NULL --(DC2Type:json)
        , tags CLOB NOT NULL --(DC2Type:json)
        , langues CLOB NOT NULL --(DC2Type:json)
        , annee DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , resume CLOB NOT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, auteurs, editeur, isbn, issn, categories, tags, langues, annee, resume) SELECT id, titre, auteurs, editeur, isbn, issn, categories, tags, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, auteurs, editeur, isbn, issn, categories, tags, langues, annee, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, auteurs CLOB NOT NULL --(DC2Type:json)
        , editeur VARCHAR(255) NOT NULL, isbn VARCHAR(255) DEFAULT NULL, issn VARCHAR(255) DEFAULT NULL, categories CLOB NOT NULL --(DC2Type:json)
        , tags CLOB NOT NULL --(DC2Type:json)
        , langues CLOB NOT NULL --(DC2Type:json)
        , année DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , resume CLOB NOT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, auteurs, editeur, isbn, issn, categories, tags, langues, année, resume) SELECT id, titre, auteurs, editeur, isbn, issn, categories, tags, langues, annee, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }
}
