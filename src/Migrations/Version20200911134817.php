<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200911134817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE future_reservation (id INT AUTO_INCREMENT NOT NULL, hotel_group_id INT NOT NULL, created INT DEFAULT NULL, reservation_id INT DEFAULT NULL, confirmation_no VARCHAR(255) DEFAULT NULL, name_id INT DEFAULT NULL, title VARCHAR(50) DEFAULT NULL, last VARCHAR(255) DEFAULT NULL, first VARCHAR(255) DEFAULT NULL, member_no VARCHAR(255) DEFAULT NULL, member_level VARCHAR(255) DEFAULT NULL, vip VARCHAR(255) DEFAULT NULL, last_stay VARCHAR(255) DEFAULT NULL, room_no VARCHAR(50) DEFAULT NULL, room_type VARCHAR(255) DEFAULT NULL, adults INT DEFAULT NULL, children INT DEFAULT NULL, arrival_date VARCHAR(255) DEFAULT NULL, departure_date VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, mobile_phone VARCHAR(255) DEFAULT NULL, gender VARCHAR(50) DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, birthdate VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, rate_code VARCHAR(50) DEFAULT NULL, market_code VARCHAR(50) DEFAULT NULL, source_code VARCHAR(50) DEFAULT NULL, travel_agent_name VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, src_name VARCHAR(255) DEFAULT NULL, preferences VARCHAR(255) DEFAULT NULL, remarks LONGTEXT DEFAULT NULL, resort VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_5F4721BFBC350B35 (hotel_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE future_reservation ADD CONSTRAINT FK_5F4721BFBC350B35 FOREIGN KEY (hotel_group_id) REFERENCES hotel_group (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE future_reservation');
    }
}
