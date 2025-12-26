<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251226170140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__emprunt AS SELECT id, exemplaire_id, user_id, statut, date_retour, date_emprunt, date_retour_effectue FROM emprunt');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaire_id INTEGER NOT NULL, user_id INTEGER NOT NULL, statut VARCHAR(255) NOT NULL, date_retour DATE NOT NULL --(DC2Type:date_immutable)
        , date_emprunt DATE NOT NULL --(DC2Type:date_immutable)
        , date_retour_effectue DATETIME DEFAULT NULL, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO emprunt (id, exemplaire_id, user_id, statut, date_retour, date_emprunt, date_retour_effectue) SELECT id, exemplaire_id, user_id, statut, date_retour, date_emprunt, date_retour_effectue FROM __temp__emprunt');
        $this->addSql('DROP TABLE __temp__emprunt');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE INDEX IDX_364071D75843AA21 ON emprunt (exemplaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__emprunt AS SELECT id, exemplaire_id, user_id, statut, date_emprunt, date_retour, date_retour_effectue FROM emprunt');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaire_id INTEGER NOT NULL, user_id INTEGER NOT NULL, statut VARCHAR(255) NOT NULL, date_emprunt DATE NOT NULL, date_retour DATE NOT NULL --(DC2Type:date_immutable)
        , date_retour_effectue DATETIME DEFAULT NULL, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO emprunt (id, exemplaire_id, user_id, statut, date_emprunt, date_retour, date_retour_effectue) SELECT id, exemplaire_id, user_id, statut, date_emprunt, date_retour, date_retour_effectue FROM __temp__emprunt');
        $this->addSql('DROP TABLE __temp__emprunt');
        $this->addSql('CREATE INDEX IDX_364071D75843AA21 ON emprunt (exemplaire_id)');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
    }
}
