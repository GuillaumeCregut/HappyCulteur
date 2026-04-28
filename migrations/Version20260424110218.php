<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424110218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hive (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, frame_number INT NOT NULL, rise_number INT NOT NULL, identification VARCHAR(30) NOT NULL, state INT NOT NULL, date DATE DEFAULT NULL, observation LONGTEXT DEFAULT NULL, qr_code VARCHAR(255) DEFAULT NULL, coord_x INT DEFAULT NULL, coord_y INT DEFAULT NULL, coord_z INT DEFAULT NULL, apiary_id INT NOT NULL, kind_id INT NOT NULL, rise_id INT DEFAULT NULL, INDEX IDX_DC6DBBF8D0E2858B (apiary_id), INDEX IDX_DC6DBBF830602CA9 (kind_id), INDEX IDX_DC6DBBF8E2EAC1E2 (rise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF8D0E2858B FOREIGN KEY (apiary_id) REFERENCES apiary (id)');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF830602CA9 FOREIGN KEY (kind_id) REFERENCES hive_kind (id)');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF8E2EAC1E2 FOREIGN KEY (rise_id) REFERENCES hive_rise (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF8D0E2858B');
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF830602CA9');
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF8E2EAC1E2');
        $this->addSql('DROP TABLE hive');
    }
}
