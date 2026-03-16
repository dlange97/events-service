<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Legacy migration marker for route color rollout';
    }

    public function up(Schema $schema): void
    {
        // Kept intentionally to preserve migration history on existing environments.
    }

    public function down(Schema $schema): void
    {
        // No-op.
    }
}
