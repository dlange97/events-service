<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306230119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('event')) {
            $eventTable = $schema->getTable('event');

            if ($eventTable->hasIndex('IDX_EVENT_OWNER')) {
                $this->addSql('DROP INDEX IDX_EVENT_OWNER ON event');
            }

            if ($eventTable->hasIndex('IDX_EVENT_START')) {
                $this->addSql('DROP INDEX IDX_EVENT_START ON event');
            }

            $this->addSql('ALTER TABLE event CHANGE description description LONGTEXT DEFAULT NULL, CHANGE start_at start_at DATETIME NOT NULL, CHANGE end_at end_at DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        }

        if ($schema->hasTable('route')) {
            $routeTable = $schema->getTable('route');

            $this->addSql('ALTER TABLE route CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');

            if ($routeTable->hasIndex('idx_owner_id') && !$routeTable->hasIndex('IDX_2C420797E3C61F9')) {
                $this->addSql('ALTER TABLE route RENAME INDEX idx_owner_id TO IDX_2C420797E3C61F9');
            }

            if ($routeTable->hasIndex('idx_event_id') && !$routeTable->hasIndex('IDX_2C4207971F7E88B')) {
                $this->addSql('ALTER TABLE route RENAME INDEX idx_event_id TO IDX_2C4207971F7E88B');
            }
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('event')) {
            $eventTable = $schema->getTable('event');

            $this->addSql('ALTER TABLE event CHANGE description description TEXT DEFAULT NULL, CHANGE start_at start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE end_at end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');

            if (!$eventTable->hasIndex('IDX_EVENT_OWNER')) {
                $this->addSql('CREATE INDEX IDX_EVENT_OWNER ON event (owner_id)');
            }

            if (!$eventTable->hasIndex('IDX_EVENT_START')) {
                $this->addSql('CREATE INDEX IDX_EVENT_START ON event (start_at)');
            }
        }

        if ($schema->hasTable('route')) {
            $routeTable = $schema->getTable('route');

            $this->addSql('ALTER TABLE route CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');

            if ($routeTable->hasIndex('IDX_2C4207971F7E88B') && !$routeTable->hasIndex('idx_event_id')) {
                $this->addSql('ALTER TABLE route RENAME INDEX IDX_2C4207971F7E88B TO idx_event_id');
            }

            if ($routeTable->hasIndex('IDX_2C420797E3C61F9') && !$routeTable->hasIndex('idx_owner_id')) {
                $this->addSql('ALTER TABLE route RENAME INDEX IDX_2C420797E3C61F9 TO idx_owner_id');
            }
        }
    }
}
