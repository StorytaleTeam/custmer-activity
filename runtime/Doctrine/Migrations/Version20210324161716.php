<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210324161716 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE memberships ADD subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE memberships ADD CONSTRAINT FK_865A47769A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_865A47769A1887DC ON memberships (subscription_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE memberships DROP CONSTRAINT FK_865A47769A1887DC');
        $this->addSql('DROP INDEX IDX_865A47769A1887DC');
        $this->addSql('ALTER TABLE memberships DROP subscription_id');
    }
}
