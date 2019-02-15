<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190215094421 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE monitoring DROP FOREIGN KEY FK_BA4F975D18F45C82');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2518F45C82');
        $this->addSql('CREATE TABLE installation (id INT AUTO_INCREMENT NOT NULL, hash VARCHAR(255) NOT NULL, last_update DATETIME DEFAULT NULL, added DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', url VARCHAR(255) NOT NULL, clean_url VARCHAR(255) NOT NULL, manager_url VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, favicon VARCHAR(1023) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, theme_color VARCHAR(255) DEFAULT NULL, contao JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', composer JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', manager JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', php_cli JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', php_web JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', config JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', composer_lock JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', self_update JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', packages JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX search_idx (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE website');
        $this->addSql('DROP INDEX IDX_BA4F975D18F45C82 ON monitoring');
        $this->addSql('DROP INDEX monitoring_unique ON monitoring');
        $this->addSql('ALTER TABLE monitoring CHANGE website_id installation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE monitoring ADD CONSTRAINT FK_BA4F975D167B88B4 FOREIGN KEY (installation_id) REFERENCES installation (id)');
        $this->addSql('CREATE INDEX IDX_BA4F975D167B88B4 ON monitoring (installation_id)');
        $this->addSql('CREATE UNIQUE INDEX monitoring_unique ON monitoring (installation_id, created_at)');
        $this->addSql('DROP INDEX IDX_527EDB2518F45C82 ON task');
        $this->addSql('ALTER TABLE task CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE website_id installation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25167B88B4 FOREIGN KEY (installation_id) REFERENCES installation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB25167B88B4 ON task (installation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE monitoring DROP FOREIGN KEY FK_BA4F975D167B88B4');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25167B88B4');
        $this->addSql('CREATE TABLE website (id INT AUTO_INCREMENT NOT NULL, last_update DATETIME DEFAULT NULL, url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, manager_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, token VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, contao JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', composer JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', manager JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', php_cli JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', php_web JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', config JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', self_update JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', packages JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', favicon VARCHAR(1023) DEFAULT NULL COLLATE utf8mb4_unicode_ci, title VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, clean_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, added DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', hash VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, theme_color VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, composer_lock JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX search_idx (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE installation');
        $this->addSql('DROP INDEX IDX_BA4F975D167B88B4 ON monitoring');
        $this->addSql('DROP INDEX monitoring_unique ON monitoring');
        $this->addSql('ALTER TABLE monitoring CHANGE installation_id website_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE monitoring ADD CONSTRAINT FK_BA4F975D18F45C82 FOREIGN KEY (website_id) REFERENCES website (id)');
        $this->addSql('CREATE INDEX IDX_BA4F975D18F45C82 ON monitoring (website_id)');
        $this->addSql('CREATE UNIQUE INDEX monitoring_unique ON monitoring (website_id, created_at)');
        $this->addSql('DROP INDEX UNIQ_527EDB25167B88B4 ON task');
        $this->addSql('ALTER TABLE task CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE installation_id website_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2518F45C82 FOREIGN KEY (installation_id) REFERENCES website (id)');
        $this->addSql('CREATE INDEX IDX_527EDB2518F45C82 ON task (website_id)');
    }
}
