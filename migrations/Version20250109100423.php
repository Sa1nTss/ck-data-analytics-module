<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109100423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE education ALTER flow TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE education ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE education ALTER result TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE education ALTER result_attempt_time TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE education ALTER flow TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE education ALTER status TYPE VARCHAR(15)');
        $this->addSql('ALTER TABLE education ALTER result TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE education ALTER result_attempt_time TYPE VARCHAR(10)');
    }
}
