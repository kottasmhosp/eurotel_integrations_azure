<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190513122832 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, hotel_group_id INT NOT NULL, created INT NOT NULL, reservation_id INT NOT NULL, confirmation_no VARCHAR(255) DEFAULT NULL, name_id INT DEFAULT NULL, title VARCHAR(50) DEFAULT NULL, last VARCHAR(255) DEFAULT NULL, first VARCHAR(255) DEFAULT NULL, member_no VARCHAR(255) DEFAULT NULL, member_level VARCHAR(255) DEFAULT NULL, vip VARCHAR(255) DEFAULT NULL, last_stay VARCHAR(255) DEFAULT NULL, room_no VARCHAR(50) DEFAULT NULL, room_type VARCHAR(255) DEFAULT NULL, adults INT DEFAULT NULL, children INT DEFAULT NULL, arrival_date VARCHAR(255) DEFAULT NULL, departure_date VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, mobile_phone VARCHAR(255) DEFAULT NULL, gender VARCHAR(50) DEFAULT NULL, nationality VARCHAR(50) DEFAULT NULL, birthdate VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, rate_code VARCHAR(50) DEFAULT NULL, market_code VARCHAR(50) DEFAULT NULL, source_code VARCHAR(50) DEFAULT NULL, travel_agent_name VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, src_name VARCHAR(255) DEFAULT NULL, preferences VARCHAR(255) DEFAULT NULL, remarks LONGTEXT DEFAULT NULL, resort VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_42C84955BC350B35 (hotel_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel_group (id INT AUTO_INCREMENT NOT NULL, created INT NOT NULL COMMENT \'UTC timestamp\', modified INT DEFAULT NULL COMMENT \'UTC timestamp\', hotel_id VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, base_url VARCHAR(255) NOT NULL, login_url VARCHAR(255) NOT NULL, inhouse_url VARCHAR(255) NOT NULL, checkout_url VARCHAR(255) DEFAULT NULL, integration_in_progress TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8E148A013243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955BC350B35 FOREIGN KEY (hotel_group_id) REFERENCES hotel_group (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955BC350B35');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE hotel_group');
    }
}
