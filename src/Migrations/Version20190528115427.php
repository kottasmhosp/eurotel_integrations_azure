<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190528115427 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE trx_code (id INT AUTO_INCREMENT NOT NULL, hotel_group_id INT NOT NULL, resort VARCHAR(255) NOT NULL, created INT NOT NULL, modified INT DEFAULT NULL, trx_code VARCHAR(50) NOT NULL, transaction_category VARCHAR(255) DEFAULT NULL, INDEX IDX_3DACB933BC350B35 (hotel_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trx_code ADD CONSTRAINT FK_3DACB933BC350B35 FOREIGN KEY (hotel_group_id) REFERENCES hotel_group (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE trx_code');
    }
}
