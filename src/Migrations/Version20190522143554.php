<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522143554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, hotel_group_id INT NOT NULL, created INT NOT NULL, resort VARCHAR(255) DEFAULT NULL, confirmation_no VARCHAR(255) DEFAULT NULL, reservation_id INT NOT NULL, name_id INT DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, room_no VARCHAR(255) DEFAULT NULL, arrival_date VARCHAR(255) DEFAULT NULL, departure_date VARCHAR(255) DEFAULT NULL, trx_code VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, gross_amount DOUBLE PRECISION NOT NULL, tc_group VARCHAR(255) DEFAULT NULL, INDEX IDX_723705D1BC350B35 (hotel_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1BC350B35 FOREIGN KEY (hotel_group_id) REFERENCES hotel_group (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE transaction');
    }
}
