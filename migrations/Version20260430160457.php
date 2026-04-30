<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430160457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE harvest (id INT AUTO_INCREMENT NOT NULL, weight DOUBLE PRECISION NOT NULL, date DATE NOT NULL, honey_kind_id INT NOT NULL, hive_id INT DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_36BDDB374924A9F1 (honey_kind_id), INDEX IDX_36BDDB37E9A48D12 (hive_id), INDEX IDX_36BDDB373484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE harvest ADD CONSTRAINT FK_36BDDB374924A9F1 FOREIGN KEY (honey_kind_id) REFERENCES honey (id)');
        $this->addSql('ALTER TABLE harvest ADD CONSTRAINT FK_36BDDB37E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE harvest ADD CONSTRAINT FK_36BDDB373484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE harvest DROP FOREIGN KEY FK_36BDDB374924A9F1');
        $this->addSql('ALTER TABLE harvest DROP FOREIGN KEY FK_36BDDB37E9A48D12');
        $this->addSql('ALTER TABLE harvest DROP FOREIGN KEY FK_36BDDB373484929B');
        $this->addSql('DROP TABLE harvest');
    }
}
