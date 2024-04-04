<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320081333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146727ACA70');
        $this->addSql('DROP INDEX IDX_6EA9A146727ACA70 ON calendar');
        $this->addSql('ALTER TABLE calendar ADD clients_id INT DEFAULT NULL, CHANGE parent_id users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A14667B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146AB014612 FOREIGN KEY (clients_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A14667B3B43D ON calendar (users_id)');
        $this->addSql('CREATE INDEX IDX_6EA9A146AB014612 ON calendar (clients_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A14667B3B43D');
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146AB014612');
        $this->addSql('DROP INDEX IDX_6EA9A14667B3B43D ON calendar');
        $this->addSql('DROP INDEX IDX_6EA9A146AB014612 ON calendar');
        $this->addSql('ALTER TABLE calendar ADD parent_id INT DEFAULT NULL, DROP users_id, DROP clients_id');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146727ACA70 FOREIGN KEY (parent_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A146727ACA70 ON calendar (parent_id)');
    }
}
