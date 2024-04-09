<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405121646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar ADD motif_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146D0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A146D0EEB819 ON calendar (motif_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146D0EEB819');
        $this->addSql('DROP INDEX IDX_6EA9A146D0EEB819 ON calendar');
        $this->addSql('ALTER TABLE calendar DROP motif_id');
    }
}
