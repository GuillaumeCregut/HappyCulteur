<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529074808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archive_datalogger (id INT AUTO_INCREMENT NOT NULL, identification VARCHAR(20) NOT NULL, date_time DATETIME NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, ext_temp DOUBLE PRECISION DEFAULT NULL, int_temp DOUBLE PRECISION DEFAULT NULL, int_hygro INT DEFAULT NULL, ext_hygro INT DEFAULT NULL, hive VARCHAR(255) DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_9E6AE5153484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive_datalogger ADD CONSTRAINT FK_9E6AE5153484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE archive_datalogger DROP FOREIGN KEY FK_9E6AE5153484929B');
        $this->addSql('DROP TABLE archive_datalogger');
    }
}
