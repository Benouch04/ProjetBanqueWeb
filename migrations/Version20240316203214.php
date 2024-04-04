<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316203214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, type_operation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD user_ope_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404554118FCCD FOREIGN KEY (user_ope_id) REFERENCES operation (id)');
        $this->addSql('CREATE INDEX IDX_C74404554118FCCD ON client (user_ope_id)');
        $this->addSql('ALTER TABLE compte ADD compte_ope_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260FED3BEC7 FOREIGN KEY (compte_ope_id) REFERENCES operation (id)');
        $this->addSql('CREATE INDEX IDX_CFF65260FED3BEC7 ON compte (compte_ope_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404554118FCCD');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260FED3BEC7');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP INDEX IDX_C74404554118FCCD ON client');
        $this->addSql('ALTER TABLE client DROP user_ope_id');
        $this->addSql('DROP INDEX IDX_CFF65260FED3BEC7 ON compte');
        $this->addSql('ALTER TABLE compte DROP compte_ope_id');
    }
}
