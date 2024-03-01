<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301134859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE motif (id INT AUTO_INCREMENT NOT NULL, libelle_motif VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users ADD users_calendar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9FDF59E3A FOREIGN KEY (users_calendar_id) REFERENCES calendar (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9FDF59E3A ON users (users_calendar_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE motif');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9FDF59E3A');
        $this->addSql('DROP INDEX IDX_1483A5E9FDF59E3A ON users');
        $this->addSql('ALTER TABLE users DROP users_calendar_id');
    }
}
