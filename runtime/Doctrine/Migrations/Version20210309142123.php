<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210309142123 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscriptions ADD duration_label VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscriptions RENAME COLUMN duration TO duration_count');
        $this->addSql('ALTER TABLE subscription_plans ADD duration_label VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscription_plans RENAME COLUMN duration TO duration_count');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscriptions DROP duration_label');
        $this->addSql('ALTER TABLE subscriptions RENAME COLUMN duration_count TO duration');
        $this->addSql('ALTER TABLE subscription_plans DROP duration_label');
        $this->addSql('ALTER TABLE subscription_plans RENAME COLUMN duration_count TO duration');
    }
}
