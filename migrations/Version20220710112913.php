<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710112913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reply_comment DROP CONSTRAINT fk_89ca3bae97e4f52d');
        $this->addSql('ALTER TABLE reply_comment DROP CONSTRAINT fk_89ca3baef2a47145');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE reply_comment');
        $this->addSql('ALTER TABLE post ADD parent_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD main_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP title');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D39C1776A FOREIGN KEY (parent_post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DBFC2AAC5 FOREIGN KEY (main_post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D39C1776A ON post (parent_post_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DBFC2AAC5 ON post (main_post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, author_id INT NOT NULL, post_id INT NOT NULL, content TEXT NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9474526cf675f31b ON comment (author_id)');
        $this->addSql('CREATE INDEX idx_9474526c4b89032c ON comment (post_id)');
        $this->addSql('CREATE TABLE reply_comment (answer_comment_id INT NOT NULL, reply_comment_id INT NOT NULL, PRIMARY KEY(answer_comment_id, reply_comment_id))');
        $this->addSql('CREATE INDEX idx_89ca3baef2a47145 ON reply_comment (reply_comment_id)');
        $this->addSql('CREATE INDEX idx_89ca3bae97e4f52d ON reply_comment (answer_comment_id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526cf675f31b FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c4b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reply_comment ADD CONSTRAINT fk_89ca3bae97e4f52d FOREIGN KEY (answer_comment_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reply_comment ADD CONSTRAINT fk_89ca3baef2a47145 FOREIGN KEY (reply_comment_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D39C1776A');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DBFC2AAC5');
        $this->addSql('DROP INDEX IDX_5A8A6C8D39C1776A');
        $this->addSql('DROP INDEX IDX_5A8A6C8DBFC2AAC5');
        $this->addSql('ALTER TABLE post ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post DROP parent_post_id');
        $this->addSql('ALTER TABLE post DROP main_post_id');
    }
}
