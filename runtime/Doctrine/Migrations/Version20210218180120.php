<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210218180120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE customers (id INT NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer_downloads (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, subscription_id INT DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, illustration_id INT NOT NULL, re_download_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E08E5CAF9395C3F3 ON customer_downloads (customer_id)');
        $this->addSql('CREATE INDEX IDX_E08E5CAF9A1887DC ON customer_downloads (subscription_id)');
        $this->addSql('CREATE TABLE customer_likes (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, illustration_id INT NOT NULL, status INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_87FF3B469395C3F3 ON customer_likes (customer_id)');
        $this->addSql('CREATE TABLE subscriptions (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, subscription_plan_id INT DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, price DOUBLE PRECISION NOT NULL, download_limit INT NOT NULL, download_remaining INT NOT NULL, status INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4778A019395C3F3 ON subscriptions (customer_id)');
        $this->addSql('CREATE INDEX IDX_4778A019B8CE200 ON subscriptions (subscription_plan_id)');
        $this->addSql('CREATE TABLE subscription_plans (id SERIAL NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, price DOUBLE PRECISION NOT NULL, download_limit INT NOT NULL, status INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE customer_downloads ADD CONSTRAINT FK_E08E5CAF9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_downloads ADD CONSTRAINT FK_E08E5CAF9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_likes ADD CONSTRAINT FK_87FF3B469395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A019395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A019B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE customer_downloads DROP CONSTRAINT FK_E08E5CAF9395C3F3');
        $this->addSql('ALTER TABLE customer_likes DROP CONSTRAINT FK_87FF3B469395C3F3');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A019395C3F3');
        $this->addSql('ALTER TABLE customer_downloads DROP CONSTRAINT FK_E08E5CAF9A1887DC');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A019B8CE200');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE customer_downloads');
        $this->addSql('DROP TABLE customer_likes');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('DROP TABLE subscription_plans');
    }
}
