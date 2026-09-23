<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190916123909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hotel_group ADD mh3as_proxy TINYINT(1) NOT NULL, CHANGE base_url base_url VARCHAR(255) DEFAULT NULL, CHANGE login_url login_url VARCHAR(255) DEFAULT NULL, CHANGE inhouse_url inhouse_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hotel_group DROP mh3as_proxy, CHANGE base_url base_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE login_url login_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE inhouse_url inhouse_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
