<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210803143310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE order_product_positions_id_seq CASCADE');
        $this->addSql('DROP TABLE order_product_positions');
        $this->addSql('DROP INDEX uniq_e52ffdee9a1887dc');
        $this->addSql('ALTER TABLE orders ADD subscription_plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD doctrine_discr VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9A1887DC ON orders (subscription_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9B8CE200 ON orders (subscription_plan_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE order_product_positions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE order_product_positions (id SERIAL NOT NULL, order_id INT DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, display_name VARCHAR(255) NOT NULL, product_type VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, count INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ac5c19aa8d9f6d38 ON order_product_positions (order_id)');
        $this->addSql('ALTER TABLE order_product_positions ADD CONSTRAINT fk_ac5c19aa8d9f6d38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE9B8CE200');
        $this->addSql('DROP INDEX IDX_E52FFDEE9A1887DC');
        $this->addSql('DROP INDEX IDX_E52FFDEE9B8CE200');
        $this->addSql('ALTER TABLE orders DROP subscription_plan_id');
        $this->addSql('ALTER TABLE orders DROP doctrine_discr');
        $this->addSql('CREATE UNIQUE INDEX uniq_e52ffdee9a1887dc ON orders (subscription_id)');
    }
}
