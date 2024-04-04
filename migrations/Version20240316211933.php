<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316211933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compte_client (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, compte_id INT DEFAULT NULL, date_ouverture DATETIME NOT NULL, solde DOUBLE PRECISION NOT NULL, montant_decouvert INT NOT NULL, INDEX IDX_1DDD5D6219EB6921 (client_id), INDEX IDX_1DDD5D62F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compte_client ADD CONSTRAINT FK_1DDD5D6219EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE compte_client ADD CONSTRAINT FK_1DDD5D62F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_client DROP FOREIGN KEY FK_1DDD5D6219EB6921');
        $this->addSql('ALTER TABLE compte_client DROP FOREIGN KEY FK_1DDD5D62F2C56620');
        $this->addSql('DROP TABLE compte_client');
    }
}
