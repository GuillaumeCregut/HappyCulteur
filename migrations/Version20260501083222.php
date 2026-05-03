<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501083222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE datalogger (id INT AUTO_INCREMENT NOT NULL, identification VARCHAR(20) NOT NULL, date_time DATETIME NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, ext_temp DOUBLE PRECISION DEFAULT NULL, int_temp DOUBLE PRECISION DEFAULT NULL, ext_hyrgo INT DEFAULT NULL, int_hygro INT DEFAULT NULL, hive_id INT DEFAULT NULL, beekeeper_id INT NOT NULL, UNIQUE INDEX UNIQ_5CF933F0E9A48D12 (hive_id), INDEX IDX_5CF933F03484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE datalogger ADD CONSTRAINT FK_5CF933F0E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE datalogger ADD CONSTRAINT FK_5CF933F03484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE datalogger DROP FOREIGN KEY FK_5CF933F0E9A48D12');
        $this->addSql('ALTER TABLE datalogger DROP FOREIGN KEY FK_5CF933F03484929B');
        $this->addSql('DROP TABLE datalogger');
    }
}
