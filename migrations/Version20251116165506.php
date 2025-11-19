<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116165506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE don (id INT AUTO_INCREMENT NOT NULL, donateur_id INT NOT NULL, date_don DATE NOT NULL, quantite INT NOT NULL, type_don VARCHAR(50) NOT NULL, apte TINYINT(1) NOT NULL, commentaire LONGTEXT DEFAULT NULL, INDEX IDX_F8F081D9A9C80E3 (donateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9A9C80E3 FOREIGN KEY (donateur_id) REFERENCES donateur (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD don_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A7B3C9061 FOREIGN KEY (don_id) REFERENCES don (id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0A7B3C9061 ON rendez_vous (don_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A7B3C9061');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9A9C80E3');
        $this->addSql('DROP TABLE don');
        $this->addSql('DROP INDEX IDX_65E8AA0A7B3C9061 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous DROP don_id');
    }
}
