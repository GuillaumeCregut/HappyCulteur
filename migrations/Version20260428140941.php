<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428140941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE swarm (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, origin INT NOT NULL, date DATE DEFAULT NULL, specy VARCHAR(60) DEFAULT NULL, capture_place VARCHAR(100) DEFAULT NULL, queen_age INT DEFAULT NULL, queen_origin VARCHAR(100) DEFAULT NULL, hive_id INT DEFAULT NULL, beekeeper_id INT NOT NULL, UNIQUE INDEX UNIQ_E9A752A1E9A48D12 (hive_id), INDEX IDX_E9A752A13484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE swarm ADD CONSTRAINT FK_E9A752A1E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE swarm ADD CONSTRAINT FK_E9A752A13484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE swarm DROP FOREIGN KEY FK_E9A752A1E9A48D12');
        $this->addSql('ALTER TABLE swarm DROP FOREIGN KEY FK_E9A752A13484929B');
        $this->addSql('DROP TABLE swarm');
    }
}
