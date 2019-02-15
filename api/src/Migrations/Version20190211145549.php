<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190211145549 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE website CHANGE last_update last_update DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD website_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2518F45C82 FOREIGN KEY (installation_id) REFERENCES website (id)');
        $this->addSql('CREATE INDEX IDX_527EDB2518F45C82 ON task (website_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2518F45C82');
        $this->addSql('DROP INDEX IDX_527EDB2518F45C82 ON task');
        $this->addSql('ALTER TABLE task DROP website_id');
        $this->addSql('ALTER TABLE website CHANGE last_update last_update DATETIME DEFAULT NULL');
    }
}
