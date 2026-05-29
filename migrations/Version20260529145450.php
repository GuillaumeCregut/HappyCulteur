<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529145450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apiary DROP FOREIGN KEY `FK_B05356E13484929B`');
        $this->addSql('DROP INDEX IDX_B05356E13484929B ON apiary');
        $this->addSql('ALTER TABLE apiary CHANGE beekeeper_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE apiary ADD CONSTRAINT FK_B05356E17E3C61F9 FOREIGN KEY (owner_id) REFERENCES apiculteur (id)');
        $this->addSql('CREATE INDEX IDX_B05356E17E3C61F9 ON apiary (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apiary DROP FOREIGN KEY FK_B05356E17E3C61F9');
        $this->addSql('DROP INDEX IDX_B05356E17E3C61F9 ON apiary');
        $this->addSql('ALTER TABLE apiary CHANGE owner_id beekeeper_id INT NOT NULL');
        $this->addSql('ALTER TABLE apiary ADD CONSTRAINT `FK_B05356E13484929B` FOREIGN KEY (beekeeper_id) REFERENCES apiculteur (id)');
        $this->addSql('CREATE INDEX IDX_B05356E13484929B ON apiary (beekeeper_id)');
    }
}
