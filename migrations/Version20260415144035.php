<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415144035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apiary (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, localisation VARCHAR(100) NOT NULL, identification VARCHAR(15) NOT NULL, is_active TINYINT NOT NULL, gps VARCHAR(30) DEFAULT NULL, path_image VARCHAR(130) DEFAULT NULL, last_picture VARCHAR(130) DEFAULT NULL, observations LONGTEXT DEFAULT NULL, beekeeper_id INT NOT NULL, INDEX IDX_B05356E13484929B (beekeeper_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apiary ADD CONSTRAINT FK_B05356E13484929B FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apiary DROP FOREIGN KEY FK_B05356E13484929B');
        $this->addSql('DROP TABLE apiary');
    }
}
