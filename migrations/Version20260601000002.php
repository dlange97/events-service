<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add color column to route table for customizable map route rendering';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('route')) {
            return;
        }

        if ($schema->getTable('route')->hasColumn('color')) {
            return;
        }

        $this->addSql("ALTER TABLE route ADD color VARCHAR(16) NOT NULL DEFAULT '#6366F1'");
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('route')) {
            return;
        }

        if (!$schema->getTable('route')->hasColumn('color')) {
            return;
        }

        $this->addSql('ALTER TABLE route DROP color');
    }
}