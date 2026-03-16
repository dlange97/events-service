<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314001000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create map_point table for user-defined map markers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE map_point (
                id          INT          NOT NULL AUTO_INCREMENT,
                owner_id    VARCHAR(36)  NOT NULL,
                name        VARCHAR(255) NOT NULL,
                description TEXT         DEFAULT NULL,
                lat         DOUBLE       NOT NULL,
                lon         DOUBLE       NOT NULL,
                created_at  DATETIME     NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                updated_at  DATETIME     NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                created_by  INT          DEFAULT NULL,
                updated_by  INT          DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_MAP_POINT_OWNER (owner_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS map_point');
    }
}
