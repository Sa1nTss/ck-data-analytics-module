<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107132231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE competence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE direction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE education_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE program_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE competence (id INT NOT NULL, name VARCHAR(255) NOT NULL, level VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE direction (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE education (id INT NOT NULL, student_id INT DEFAULT NULL, competence_id INT DEFAULT NULL, program_id INT DEFAULT NULL, direction_id INT DEFAULT NULL, date DATE NOT NULL, flow VARCHAR(10) NOT NULL, status VARCHAR(15) NOT NULL, inopolis_id VARCHAR(50) NOT NULL, competence_level VARCHAR(255) DEFAULT NULL, competence_grow_level VARCHAR(255) DEFAULT NULL, result VARCHAR(10) DEFAULT NULL, attempts INT DEFAULT NULL, result_attempt_time VARCHAR(10) DEFAULT NULL, stage INT NOT NULL, external_proctoring VARCHAR(255) DEFAULT NULL, internal_proctoring VARCHAR(255) DEFAULT NULL, proctoring_photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB0A5ED2CB944F1A ON education (student_id)');
        $this->addSql('CREATE INDEX IDX_DB0A5ED215761DAB ON education (competence_id)');
        $this->addSql('CREATE INDEX IDX_DB0A5ED23EB8070A ON education (program_id)');
        $this->addSql('CREATE INDEX IDX_DB0A5ED2AF73D997 ON education (direction_id)');
        $this->addSql('CREATE TABLE program (id INT NOT NULL, name VARCHAR(255) NOT NULL, it BOOLEAN DEFAULT NULL, hours INT NOT NULL, realization_time INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, fio VARCHAR(255) NOT NULL, inopolis_id VARCHAR(255) NOT NULL, snils VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, birthday DATE NOT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED215761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED23EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2AF73D997 FOREIGN KEY (direction_id) REFERENCES direction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE competence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE direction_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE education_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE program_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE student_id_seq CASCADE');
        $this->addSql('ALTER TABLE education DROP CONSTRAINT FK_DB0A5ED2CB944F1A');
        $this->addSql('ALTER TABLE education DROP CONSTRAINT FK_DB0A5ED215761DAB');
        $this->addSql('ALTER TABLE education DROP CONSTRAINT FK_DB0A5ED23EB8070A');
        $this->addSql('ALTER TABLE education DROP CONSTRAINT FK_DB0A5ED2AF73D997');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE direction');
        $this->addSql('DROP TABLE education');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE student');
    }
}
