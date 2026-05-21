<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521165007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apiary (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, localisation VARCHAR(100) NOT NULL, identification VARCHAR(15) NOT NULL, is_active TINYINT NOT NULL, gps VARCHAR(30) DEFAULT NULL, path_image VARCHAR(130) DEFAULT NULL, last_picture VARCHAR(130) DEFAULT NULL, observations LONGTEXT DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_B05356E13484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apiculteur (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, code_ape VARCHAR(5) DEFAULT NULL, code_api VARCHAR(10) NOT NULL, siret VARCHAR(20) DEFAULT NULL, numagri VARCHAR(30) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, city VARCHAR(150) NOT NULL, street VARCHAR(255) NOT NULL, street_number VARCHAR(10) NOT NULL, zip_code VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN (login), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE datalogger (id INT AUTO_INCREMENT NOT NULL, identification VARCHAR(20) NOT NULL, date_time DATETIME NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, ext_temp DOUBLE PRECISION DEFAULT NULL, int_temp DOUBLE PRECISION DEFAULT NULL, ext_hyrgo INT DEFAULT NULL, int_hygro INT DEFAULT NULL, hive_id INT DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_5CF933F0E9A48D12 (hive_id), INDEX IDX_5CF933F03484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disease (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE harvest (id INT AUTO_INCREMENT NOT NULL, weight DOUBLE PRECISION NOT NULL, date DATE NOT NULL, honey_kind_id INT NOT NULL, hive_id INT DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_36BDDB374924A9F1 (honey_kind_id), INDEX IDX_36BDDB37E9A48D12 (hive_id), INDEX IDX_36BDDB373484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hive (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, frame_number INT NOT NULL, rise_number INT NOT NULL, identification VARCHAR(30) NOT NULL, state INT NOT NULL, date DATE DEFAULT NULL, observation LONGTEXT DEFAULT NULL, qr_code VARCHAR(255) DEFAULT NULL, coord_x INT DEFAULT NULL, coord_y INT DEFAULT NULL, coord_z INT DEFAULT NULL, data_logger_name VARCHAR(255) DEFAULT NULL, apiary_id INT NOT NULL, kind_id INT NOT NULL, rise_id INT DEFAULT NULL, INDEX IDX_DC6DBBF8D0E2858B (apiary_id), INDEX IDX_DC6DBBF830602CA9 (kind_id), INDEX IDX_DC6DBBF8E2EAC1E2 (rise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hive_kind (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, picture VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hive_rise (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE honey (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, provider VARCHAR(150) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, description LONGTEXT NOT NULL, beekeeper_id INT NOT NULL, INDEX IDX_6117D13B3484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE swarm (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, origin INT NOT NULL, date DATE DEFAULT NULL, specy VARCHAR(60) DEFAULT NULL, capture_place VARCHAR(100) DEFAULT NULL, queen_age DATE DEFAULT NULL, queen_origin VARCHAR(100) DEFAULT NULL, hive_id INT DEFAULT NULL, beekeeper_id INT NOT NULL, UNIQUE INDEX UNIQ_E9A752A1E9A48D12 (hive_id), INDEX IDX_E9A752A13484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, weather INT NOT NULL, is_disease TINYINT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, hygrometry INT DEFAULT NULL, is_works_to_do TINYINT NOT NULL, is_feeded TINYINT NOT NULL, feeding VARCHAR(100) DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, is_queen_visible TINYINT NOT NULL, population VARCHAR(255) DEFAULT NULL, behaviour VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, hive_id INT NOT NULL, disease_id INT DEFAULT NULL, INDEX IDX_437EE939E9A48D12 (hive_id), INDEX IDX_437EE939D8355341 (disease_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apiary ADD CONSTRAINT FK_B05356E13484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
        $this->addSql('ALTER TABLE datalogger ADD CONSTRAINT FK_5CF933F0E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE datalogger ADD CONSTRAINT FK_5CF933F03484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
        $this->addSql('ALTER TABLE harvest ADD CONSTRAINT FK_36BDDB374924A9F1 FOREIGN KEY (honey_kind_id) REFERENCES honey (id)');
        $this->addSql('ALTER TABLE harvest ADD CONSTRAINT FK_36BDDB37E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE harvest ADD CONSTRAINT FK_36BDDB373484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF8D0E2858B FOREIGN KEY (apiary_id) REFERENCES apiary (id)');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF830602CA9 FOREIGN KEY (kind_id) REFERENCES hive_kind (id)');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF8E2EAC1E2 FOREIGN KEY (rise_id) REFERENCES hive_rise (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B3484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
        $this->addSql('ALTER TABLE swarm ADD CONSTRAINT FK_E9A752A1E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE swarm ADD CONSTRAINT FK_E9A752A13484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939E9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939D8355341 FOREIGN KEY (disease_id) REFERENCES disease (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apiary DROP FOREIGN KEY FK_B05356E13484929B');
        $this->addSql('ALTER TABLE datalogger DROP FOREIGN KEY FK_5CF933F0E9A48D12');
        $this->addSql('ALTER TABLE datalogger DROP FOREIGN KEY FK_5CF933F03484929B');
        $this->addSql('ALTER TABLE harvest DROP FOREIGN KEY FK_36BDDB374924A9F1');
        $this->addSql('ALTER TABLE harvest DROP FOREIGN KEY FK_36BDDB37E9A48D12');
        $this->addSql('ALTER TABLE harvest DROP FOREIGN KEY FK_36BDDB373484929B');
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF8D0E2858B');
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF830602CA9');
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF8E2EAC1E2');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B3484929B');
        $this->addSql('ALTER TABLE swarm DROP FOREIGN KEY FK_E9A752A1E9A48D12');
        $this->addSql('ALTER TABLE swarm DROP FOREIGN KEY FK_E9A752A13484929B');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939E9A48D12');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939D8355341');
        $this->addSql('DROP TABLE apiary');
        $this->addSql('DROP TABLE apiculteur');
        $this->addSql('DROP TABLE datalogger');
        $this->addSql('DROP TABLE disease');
        $this->addSql('DROP TABLE harvest');
        $this->addSql('DROP TABLE hive');
        $this->addSql('DROP TABLE hive_kind');
        $this->addSql('DROP TABLE hive_rise');
        $this->addSql('DROP TABLE honey');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE swarm');
        $this->addSql('DROP TABLE visit');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
