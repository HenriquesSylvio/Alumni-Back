<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220810122416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE job (id INT NOT NULL, author_id INT NOT NULL, faculty_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, city VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, compensation VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8F675F31B ON job (author_id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8680CAB68 ON job (faculty_id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE job_id_seq CASCADE');
        $this->addSql('DROP TABLE job');
    }
}
