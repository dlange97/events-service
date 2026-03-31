<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260331193249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE shared_with_user_ids shared_with_user_ids JSON NOT NULL');
        $this->addSql('ALTER TABLE map_point CHANGE description description LONGTEXT DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE map_point RENAME INDEX idx_map_point_owner TO IDX_3753BC487E3C61F9');
        $this->addSql('ALTER TABLE route CHANGE color color VARCHAR(16) DEFAULT \'#6366f1\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE shared_with_user_ids shared_with_user_ids JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE map_point CHANGE description description TEXT DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE map_point RENAME INDEX idx_3753bc487e3c61f9 TO IDX_MAP_POINT_OWNER');
        $this->addSql('ALTER TABLE route CHANGE color color VARCHAR(16) DEFAULT \'#6366F1\' NOT NULL');
    }
}
