<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129195308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ckgroup_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE faculty_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "group_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE university_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ckgroup (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE faculty (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "group" (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE university (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE student ADD student_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD ck_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD university_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF334DDF95DC FOREIGN KEY (student_group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BD2B36DB FOREIGN KEY (ck_group_id) REFERENCES ckgroup (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33309D1878 FOREIGN KEY (university_id) REFERENCES university (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B723AF334DDF95DC ON student (student_group_id)');
        $this->addSql('CREATE INDEX IDX_B723AF33BD2B36DB ON student (ck_group_id)');
        $this->addSql('CREATE INDEX IDX_B723AF33309D1878 ON student (university_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33BD2B36DB');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF334DDF95DC');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33309D1878');
        $this->addSql('DROP SEQUENCE ckgroup_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE faculty_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "group_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE university_id_seq CASCADE');
        $this->addSql('DROP TABLE ckgroup');
        $this->addSql('DROP TABLE faculty');
        $this->addSql('DROP TABLE "group"');
        $this->addSql('DROP TABLE university');
        $this->addSql('DROP INDEX IDX_B723AF334DDF95DC');
        $this->addSql('DROP INDEX IDX_B723AF33BD2B36DB');
        $this->addSql('DROP INDEX IDX_B723AF33309D1878');
        $this->addSql('ALTER TABLE student DROP student_group_id');
        $this->addSql('ALTER TABLE student DROP ck_group_id');
        $this->addSql('ALTER TABLE student DROP university_id');
    }
}
