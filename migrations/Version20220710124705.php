<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710124705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE faculty_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7F675F31B ON event (author_id)');
        $this->addSql('CREATE TABLE faculty (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE like_post (post_id INT NOT NULL, like_by_id INT NOT NULL, PRIMARY KEY(post_id, like_by_id))');
        $this->addSql('CREATE INDEX IDX_83FFB0F34B89032C ON like_post (post_id)');
        $this->addSql('CREATE INDEX IDX_83FFB0F31D8309E3 ON like_post (like_by_id)');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, sent_by_id INT NOT NULL, received_by_id INT NOT NULL, content TEXT NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FA45BB98C ON message (sent_by_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F6F8DDD17 ON message (received_by_id)');
        $this->addSql('CREATE TABLE participate (event_id INT NOT NULL, participant_id INT NOT NULL, PRIMARY KEY(event_id, participant_id))');
        $this->addSql('CREATE INDEX IDX_D02B13871F7E88B ON participate (event_id)');
        $this->addSql('CREATE INDEX IDX_D02B1389D1C3019 ON participate (participant_id)');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, author_id INT NOT NULL, main_post_id INT DEFAULT NULL, content TEXT NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DBFC2AAC5 ON post (main_post_id)');
        $this->addSql('CREATE TABLE subscribe (subscription_id INT NOT NULL, subscriber_id INT NOT NULL, PRIMARY KEY(subscription_id, subscriber_id))');
        $this->addSql('CREATE INDEX IDX_68B95F3E9A1887DC ON subscribe (subscription_id)');
        $this->addSql('CREATE INDEX IDX_68B95F3E7808B1AD ON subscribe (subscriber_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, faculty_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, biography VARCHAR(255) DEFAULT NULL, url_profile_picture TEXT DEFAULT NULL, accept_account BOOLEAN NOT NULL, username VARCHAR(255) NOT NULL, promo INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649680CAB68 ON "user" (faculty_id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE like_post ADD CONSTRAINT FK_83FFB0F34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE like_post ADD CONSTRAINT FK_83FFB0F31D8309E3 FOREIGN KEY (like_by_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA45BB98C FOREIGN KEY (sent_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B13871F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B1389D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DBFC2AAC5 FOREIGN KEY (main_post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3E9A1887DC FOREIGN KEY (subscription_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3E7808B1AD FOREIGN KEY (subscriber_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE participate DROP CONSTRAINT FK_D02B13871F7E88B');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649680CAB68');
        $this->addSql('ALTER TABLE like_post DROP CONSTRAINT FK_83FFB0F34B89032C');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DBFC2AAC5');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7F675F31B');
        $this->addSql('ALTER TABLE like_post DROP CONSTRAINT FK_83FFB0F31D8309E3');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FA45BB98C');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F6F8DDD17');
        $this->addSql('ALTER TABLE participate DROP CONSTRAINT FK_D02B1389D1C3019');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE subscribe DROP CONSTRAINT FK_68B95F3E9A1887DC');
        $this->addSql('ALTER TABLE subscribe DROP CONSTRAINT FK_68B95F3E7808B1AD');
        $this->addSql('DROP SEQUENCE event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE faculty_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE faculty');
        $this->addSql('DROP TABLE like_post');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE participate');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('DROP TABLE "user"');
    }
}
