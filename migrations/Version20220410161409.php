<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220410161409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_post (post_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(post_id, user_id))');
        $this->addSql('CREATE INDEX IDX_83FFB0F34B89032C ON like_post (post_id)');
        $this->addSql('CREATE INDEX IDX_83FFB0F3A76ED395 ON like_post (user_id)');
        $this->addSql('ALTER TABLE like_post ADD CONSTRAINT FK_83FFB0F34B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE like_post ADD CONSTRAINT FK_83FFB0F3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE like_post');
    }
}
