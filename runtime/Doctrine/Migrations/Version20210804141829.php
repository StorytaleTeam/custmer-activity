<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804141829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE subscription_plans_id_seq CASCADE');
        $this->addSql('CREATE TABLE products (id SERIAL NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, total_price DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, doctrine_discr VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE subscription_plans DROP created_date');
        $this->addSql('ALTER TABLE subscription_plans DROP price');
        $this->addSql('ALTER TABLE subscription_plans ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE subscription_plans ADD CONSTRAINT FK_CF5F99A2BF396750 FOREIGN KEY (id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdee9b8ce200');
        $this->addSql('DROP INDEX idx_e52ffdee9b8ce200');
        $this->addSql('ALTER TABLE orders DROP subscription_plan_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscription_plans DROP CONSTRAINT FK_CF5F99A2BF396750');
        $this->addSql('CREATE SEQUENCE subscription_plans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE products');
        $this->addSql('ALTER TABLE subscription_plans ADD created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE subscription_plans ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('CREATE SEQUENCE subscription_plans_id_seq');
        $this->addSql('SELECT setval(\'subscription_plans_id_seq\', (SELECT MAX(id) FROM subscription_plans))');
        $this->addSql('ALTER TABLE subscription_plans ALTER id SET DEFAULT nextval(\'subscription_plans_id_seq\')');
        $this->addSql('ALTER TABLE orders ADD subscription_plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_e52ffdee9b8ce200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e52ffdee9b8ce200 ON orders (subscription_plan_id)');
    }
}
