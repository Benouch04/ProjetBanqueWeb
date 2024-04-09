<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240406190331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motif_pj ADD motif_id INT DEFAULT NULL, ADD piece_justif_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motif_pj ADD CONSTRAINT FK_AC563C06D0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id)');
        $this->addSql('ALTER TABLE motif_pj ADD CONSTRAINT FK_AC563C06A5292946 FOREIGN KEY (piece_justif_id) REFERENCES piece_justif (id)');
        $this->addSql('CREATE INDEX IDX_AC563C06D0EEB819 ON motif_pj (motif_id)');
        $this->addSql('CREATE INDEX IDX_AC563C06A5292946 ON motif_pj (piece_justif_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motif_pj DROP FOREIGN KEY FK_AC563C06D0EEB819');
        $this->addSql('ALTER TABLE motif_pj DROP FOREIGN KEY FK_AC563C06A5292946');
        $this->addSql('DROP INDEX IDX_AC563C06D0EEB819 ON motif_pj');
        $this->addSql('DROP INDEX IDX_AC563C06A5292946 ON motif_pj');
        $this->addSql('ALTER TABLE motif_pj DROP motif_id, DROP piece_justif_id');
    }
}
