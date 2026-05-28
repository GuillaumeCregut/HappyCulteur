<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260522111036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archive_visit (id INT AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT NULL, hive VARCHAR(255) NOT NULL, weather VARCHAR(100) NOT NULL, is_disease TINYINT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, hygrometry INT DEFAULT NULL, is_work_to_do TINYINT NOT NULL, is_feeded TINYINT NOT NULL, feeding VARCHAR(255) DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, is_queen_visible TINYINT NOT NULL, population VARCHAR(255) DEFAULT NULL, behaviour VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, disease VARCHAR(255) DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_249386233484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive_visit ADD CONSTRAINT FK_249386233484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE archive_visit DROP FOREIGN KEY FK_249386233484929B');
        $this->addSql('DROP TABLE archive_visit');
    }
}
