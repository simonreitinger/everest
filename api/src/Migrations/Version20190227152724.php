<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190227152724 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE installation DROP contao, DROP composer, DROP manager, DROP php_cli, DROP php_web, DROP config, DROP composer_lock, DROP self_update, DROP packages, CHANGE last_update last_update DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE output output JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE software CHANGE versions versions JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE installation ADD contao MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD composer MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD manager MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD php_cli MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD php_web MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD config MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD composer_lock MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD self_update MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD packages MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE last_update last_update DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE software CHANGE versions versions MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE task CHANGE output output MEDIUMTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}
