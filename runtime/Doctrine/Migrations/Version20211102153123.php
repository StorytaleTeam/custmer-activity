<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211102153123 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscription_plans ADD charge_period_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_plans ADD charge_period_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_plans ALTER duration_count DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription_plans ALTER duration_label DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscription_plans DROP charge_period_label');
        $this->addSql('ALTER TABLE subscription_plans DROP charge_period_count');
        $this->addSql('ALTER TABLE subscription_plans ALTER duration_label SET NOT NULL');
        $this->addSql('ALTER TABLE subscription_plans ALTER duration_count SET NOT NULL');
    }
}
