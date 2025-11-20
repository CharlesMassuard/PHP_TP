<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120174144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaires AS SELECT id, ouvrage_id, cote, etat, emplacement, disponibilite FROM exemplaires');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ouvrage_id INTEGER NOT NULL, cote VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, emplacement VARCHAR(255) NOT NULL, disponibilite BOOLEAN NOT NULL, CONSTRAINT FK_551C55F15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaires (id, ouvrage_id, cote, etat, emplacement, disponibilite) SELECT id, ouvrage_id, cote, etat, emplacement, disponibilite FROM __temp__exemplaires');
        $this->addSql('DROP TABLE __temp__exemplaires');
        $this->addSql('CREATE INDEX IDX_551C55F15D884B5 ON exemplaires (ouvrage_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_551C55F3DD722C9 ON exemplaires (cote)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaires AS SELECT id, ouvrage_id, cote, etat, emplacement, disponibilite FROM exemplaires');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ouvrage_id INTEGER NOT NULL, cote VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, emplacement VARCHAR(255) NOT NULL, disponibilite BOOLEAN NOT NULL, CONSTRAINT FK_551C55F15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaires (id, ouvrage_id, cote, etat, emplacement, disponibilite) SELECT id, ouvrage_id, cote, etat, emplacement, disponibilite FROM __temp__exemplaires');
        $this->addSql('DROP TABLE __temp__exemplaires');
        $this->addSql('CREATE INDEX IDX_551C55F15D884B5 ON exemplaires (ouvrage_id)');
    }
}
