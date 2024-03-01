<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301211341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146727ACA70 FOREIGN KEY (parent_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A146727ACA70 ON calendar (parent_id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A40A2C8');
        $this->addSql('DROP INDEX IDX_1483A5E9A40A2C8 ON users');
        $this->addSql('ALTER TABLE users DROP calendar_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146727ACA70');
        $this->addSql('DROP INDEX IDX_6EA9A146727ACA70 ON calendar');
        $this->addSql('ALTER TABLE calendar DROP parent_id');
        $this->addSql('ALTER TABLE users ADD calendar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9A40A2C8 ON users (calendar_id)');
    }
}
