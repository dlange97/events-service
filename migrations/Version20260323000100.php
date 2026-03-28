<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add shared user IDs for events';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('event')) {
            return;
        }

        if ($schema->getTable('event')->hasColumn('shared_with_user_ids')) {
            return;
        }

        $this->addSql("ALTER TABLE event ADD shared_with_user_ids JSON NOT NULL COMMENT '(DC2Type:json)'");
        $this->addSql("UPDATE event SET shared_with_user_ids = '[]'");
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('event')) {
            return;
        }

        if (!$schema->getTable('event')->hasColumn('shared_with_user_ids')) {
            return;
        }

        $this->addSql('ALTER TABLE event DROP shared_with_user_ids');
    }
}
