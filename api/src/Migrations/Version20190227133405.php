<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190227133405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json_array)\', CHANGE output output MEDIUMTEXT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE installation CHANGE last_update last_update DATETIME DEFAULT NULL, CHANGE contao contao MEDIUMTEXT DEFAULT NULL, CHANGE composer composer MEDIUMTEXT DEFAULT NULL, CHANGE manager manager MEDIUMTEXT DEFAULT NULL, CHANGE php_cli php_cli MEDIUMTEXT DEFAULT NULL, CHANGE php_web php_web MEDIUMTEXT DEFAULT NULL, CHANGE config config MEDIUMTEXT DEFAULT NULL, CHANGE composer_lock composer_lock MEDIUMTEXT DEFAULT NULL, CHANGE self_update self_update MEDIUMTEXT DEFAULT NULL, CHANGE packages packages MEDIUMTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE software CHANGE versions versions MEDIUMTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE installation CHANGE last_update last_update DATETIME DEFAULT NULL, CHANGE contao contao JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE composer composer JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE manager manager JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE php_cli php_cli JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE php_web php_web JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE config config JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE composer_lock composer_lock JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE self_update self_update JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE packages packages JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE software CHANGE versions versions JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE task CHANGE config config VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE output output VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}
