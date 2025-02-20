<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130122021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student ADD faculty_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B723AF33680CAB68 ON student (faculty_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33680CAB68');
        $this->addSql('DROP INDEX IDX_B723AF33680CAB68');
        $this->addSql('ALTER TABLE student DROP faculty_id');
    }
}
