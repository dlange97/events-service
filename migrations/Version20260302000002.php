<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260302000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create route table for storing drawn routes with GeoJSON';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS route (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description LONGTEXT NULL,
            geo_json JSON NOT NULL,
            distance_meters DOUBLE PRECISION NULL,
            duration_minutes INT NULL,
            waypoints JSON NOT NULL,
            owner_id VARCHAR(36) NOT NULL,
            event_id INT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            created_by INT NULL,
            updated_by INT NULL,
            PRIMARY KEY(id),
            INDEX idx_owner_id (owner_id),
            INDEX idx_event_id (event_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS route');
    }
}
