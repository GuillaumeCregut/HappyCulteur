<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528170743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archive_harvest (id INT AUTO_INCREMENT NOT NULL, weight DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, honey_kind VARCHAR(255) NOT NULL, hive VARCHAR(255) NOT NULL, beekeeper_id INT NOT NULL, INDEX IDX_5B9D3AC83484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive_harvest ADD CONSTRAINT FK_5B9D3AC83484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE archive_harvest DROP FOREIGN KEY FK_5B9D3AC83484929B');
        $this->addSql('DROP TABLE archive_harvest');
    }
}
