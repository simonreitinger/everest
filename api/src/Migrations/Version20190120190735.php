<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190120190735 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE website-list CHANGE description description VARCHAR(1000) NOT NULL, CHANGE repo repo VARCHAR(255) NOT NULL, CHANGE manager_username manager_username VARCHAR(255) DEFAULT NULL, CHANGE manager_password manager_password VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE website-list CHANGE description description VARCHAR(1000) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE repo repo VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE manager_username manager_username VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE manager_password manager_password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
