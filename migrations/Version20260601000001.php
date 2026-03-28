<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create events schema: event table with location and audit columns';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('event')) {
            return;
        }

        $this->addSql('
            CREATE TABLE event (
                id                   INT             NOT NULL AUTO_INCREMENT,
                owner_id             VARCHAR(36)     NOT NULL,
                title                VARCHAR(255)    NOT NULL,
                description          LONGTEXT        DEFAULT NULL,
                start_at             DATETIME        NOT NULL,
                end_at               DATETIME        DEFAULT NULL,
                location_name        VARCHAR(512)    DEFAULT NULL,
                location_lat         DOUBLE          DEFAULT NULL,
                location_lon         DOUBLE          DEFAULT NULL,
                shared_with_user_ids JSON            NOT NULL COMMENT \'(DC2Type:json)\',
                created_at           DATETIME        NOT NULL,
                updated_at           DATETIME        NOT NULL,
                created_by           INT             DEFAULT NULL,
                updated_by           INT             DEFAULT NULL,
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql("UPDATE event SET shared_with_user_ids = '[]' WHERE shared_with_user_ids IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS event');
    }
}
