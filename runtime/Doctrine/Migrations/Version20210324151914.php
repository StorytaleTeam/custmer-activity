<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210324151914 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE memberships (id SERIAL NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount_received DOUBLE PRECISION NOT NULL, download_limit INT NOT NULL, status INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cycle_number INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE customer_downloads DROP CONSTRAINT fk_e08e5caf9a1887dc');
        $this->addSql('DROP INDEX idx_e08e5caf9a1887dc');
        $this->addSql('ALTER TABLE customer_downloads RENAME COLUMN subscription_id TO membership_id');
        $this->addSql('ALTER TABLE customer_downloads ADD CONSTRAINT FK_E08E5CAF1FB354CD FOREIGN KEY (membership_id) REFERENCES memberships (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E08E5CAF1FB354CD ON customer_downloads (membership_id)');
        $this->addSql('ALTER TABLE subscriptions ADD auto_renewal BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD current_membership_cycle INT NOT NULL');
        $this->addSql('ALTER TABLE subscriptions DROP name');
        $this->addSql('ALTER TABLE subscriptions DROP duration_count');
        $this->addSql('ALTER TABLE subscriptions DROP price');
        $this->addSql('ALTER TABLE subscriptions DROP download_limit');
        $this->addSql('ALTER TABLE subscriptions DROP start_date');
        $this->addSql('ALTER TABLE subscriptions DROP end_date');
        $this->addSql('ALTER TABLE subscriptions DROP duration_label');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE customer_downloads DROP CONSTRAINT FK_E08E5CAF1FB354CD');
        $this->addSql('DROP TABLE memberships');
        $this->addSql('DROP INDEX IDX_E08E5CAF1FB354CD');
        $this->addSql('ALTER TABLE customer_downloads RENAME COLUMN membership_id TO subscription_id');
        $this->addSql('ALTER TABLE customer_downloads ADD CONSTRAINT fk_e08e5caf9a1887dc FOREIGN KEY (subscription_id) REFERENCES subscriptions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e08e5caf9a1887dc ON customer_downloads (subscription_id)');
        $this->addSql('ALTER TABLE subscriptions ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD download_limit INT NOT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD duration_label VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscriptions DROP auto_renewal');
        $this->addSql('ALTER TABLE subscriptions RENAME COLUMN current_membership_cycle TO duration_count');
    }
}
