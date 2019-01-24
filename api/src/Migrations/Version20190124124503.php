<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190124124503 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE monitoring ADD website_id INT DEFAULT NULL, DROP website');
        $this->addSql('ALTER TABLE monitoring ADD CONSTRAINT FK_BA4F975D18F45C82 FOREIGN KEY (website_id) REFERENCES website (id)');
        $this->addSql('CREATE INDEX IDX_BA4F975D18F45C82 ON monitoring (website_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE monitoring DROP FOREIGN KEY FK_BA4F975D18F45C82');
        $this->addSql('DROP INDEX IDX_BA4F975D18F45C82 ON monitoring');
        $this->addSql('ALTER TABLE monitoring ADD website INT NOT NULL, DROP website_id');
    }
}
