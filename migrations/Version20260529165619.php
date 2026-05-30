<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529165619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apiary_apiculteur (apiary_id INT NOT NULL, apiculteur_id INT NOT NULL, INDEX IDX_B50A5D9CD0E2858B (apiary_id), INDEX IDX_B50A5D9CFD789156 (apiculteur_id), PRIMARY KEY (apiary_id, apiculteur_id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apiary_apiculteur ADD CONSTRAINT FK_B50A5D9CD0E2858B FOREIGN KEY (apiary_id) REFERENCES apiary (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE apiary_apiculteur ADD CONSTRAINT FK_B50A5D9CFD789156 FOREIGN KEY (apiculteur_id) REFERENCES apiculteur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apiary_apiculteur DROP FOREIGN KEY FK_B50A5D9CD0E2858B');
        $this->addSql('ALTER TABLE apiary_apiculteur DROP FOREIGN KEY FK_B50A5D9CFD789156');
        $this->addSql('DROP TABLE apiary_apiculteur');
    }
}
