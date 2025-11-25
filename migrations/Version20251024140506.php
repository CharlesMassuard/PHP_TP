<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024140506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaires AS SELECT id, cote, etat, emplacement, disponibilite FROM exemplaires');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ouvrage_id INTEGER NOT NULL, cote VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, emplacement VARCHAR(255) NOT NULL, disponibilite VARCHAR(255) NOT NULL, CONSTRAINT FK_551C55F15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaires (id, cote, etat, emplacement, disponibilite) SELECT id, cote, etat, emplacement, disponibilite FROM __temp__exemplaires');
        $this->addSql('DROP TABLE __temp__exemplaires');
        $this->addSql('CREATE INDEX IDX_551C55F15D884B5 ON exemplaires (ouvrage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaires AS SELECT id, cote, etat, emplacement, disponibilite FROM exemplaires');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cote VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, emplacement VARCHAR(255) NOT NULL, disponibilite VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO exemplaires (id, cote, etat, emplacement, disponibilite) SELECT id, cote, etat, emplacement, disponibilite FROM __temp__exemplaires');
        $this->addSql('DROP TABLE __temp__exemplaires');
    }
}
