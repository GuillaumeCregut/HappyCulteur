<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260502143612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE datalogger DROP INDEX UNIQ_5CF933F0E9A48D12, ADD INDEX IDX_5CF933F0E9A48D12 (hive_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE datalogger DROP INDEX IDX_5CF933F0E9A48D12, ADD UNIQUE INDEX UNIQ_5CF933F0E9A48D12 (hive_id)');
    }
}
