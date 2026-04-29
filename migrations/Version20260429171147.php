<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260429171147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, weather INT NOT NULL, is_disease TINYINT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, hygrometry INT DEFAULT NULL, is_works_to_do TINYINT NOT NULL, is_feeded TINYINT NOT NULL, feeding VARCHAR(100) DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, is_queen_visible TINYINT NOT NULL, population VARCHAR(255) DEFAULT NULL, behaviour VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, hive_id INT NOT NULL, disease_id INT DEFAULT NULL, INDEX IDX_437EE939E9A48D12 (hive_id), INDEX IDX_437EE939D8355341 (disease_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939D8355341 FOREIGN KEY (disease_id) REFERENCES desease (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939E9A48D12');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939D8355341');
        $this->addSql('DROP TABLE visit');
    }
}
