<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408212455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE motif_piece_justif (motif_id INT NOT NULL, piece_justif_id INT NOT NULL, INDEX IDX_1146D387D0EEB819 (motif_id), INDEX IDX_1146D387A5292946 (piece_justif_id), PRIMARY KEY(motif_id, piece_justif_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE motif_piece_justif ADD CONSTRAINT FK_1146D387D0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motif_piece_justif ADD CONSTRAINT FK_1146D387A5292946 FOREIGN KEY (piece_justif_id) REFERENCES piece_justif (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motif_pj DROP FOREIGN KEY FK_AC563C06A5292946');
        $this->addSql('ALTER TABLE motif_pj DROP FOREIGN KEY FK_AC563C06D0EEB819');
        $this->addSql('DROP TABLE motif_pj');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE motif_pj (id INT AUTO_INCREMENT NOT NULL, motif_id INT DEFAULT NULL, piece_justif_id INT DEFAULT NULL, INDEX IDX_AC563C06D0EEB819 (motif_id), INDEX IDX_AC563C06A5292946 (piece_justif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE motif_pj ADD CONSTRAINT FK_AC563C06A5292946 FOREIGN KEY (piece_justif_id) REFERENCES piece_justif (id)');
        $this->addSql('ALTER TABLE motif_pj ADD CONSTRAINT FK_AC563C06D0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id)');
        $this->addSql('ALTER TABLE motif_piece_justif DROP FOREIGN KEY FK_1146D387D0EEB819');
        $this->addSql('ALTER TABLE motif_piece_justif DROP FOREIGN KEY FK_1146D387A5292946');
        $this->addSql('DROP TABLE motif_piece_justif');
    }
}
