<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260331000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add instance_id column to event, map_point and route tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD instance_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE map_point ADD instance_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE route ADD instance_id VARCHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE route DROP instance_id');
        $this->addSql('ALTER TABLE map_point DROP instance_id');
        $this->addSql('ALTER TABLE event DROP instance_id');
    }
}
