<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707101734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE faculty_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE faculty (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "user" ADD faculty_id INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649680CAB68 ON "user" (faculty_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649680CAB68');
        $this->addSql('DROP SEQUENCE faculty_id_seq CASCADE');
        $this->addSql('DROP TABLE faculty');
        $this->addSql('DROP INDEX IDX_8D93D649680CAB68');
        $this->addSql('ALTER TABLE "user" DROP faculty_id');
    }
}
