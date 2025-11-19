<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116172954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD niveau_alerte INT NOT NULL, CHANGE niveua_actuel niveau_actuel INT NOT NULL, CHANGE dernier_mise_ajour derniere_mise_ajour DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD niveua_actuel INT NOT NULL, DROP niveau_actuel, DROP niveau_alerte, CHANGE derniere_mise_ajour dernier_mise_ajour DATETIME NOT NULL');
    }
}
