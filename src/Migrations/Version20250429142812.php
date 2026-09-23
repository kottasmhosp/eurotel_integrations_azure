<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429142812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE future_reservation ADD crs_no VARCHAR(255) DEFAULT NULL, ADD posting_allowed_yn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD crs_no VARCHAR(255) DEFAULT NULL, ADD posting_allowed_yn VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE future_reservation DROP crs_no, DROP posting_allowed_yn');
        $this->addSql('ALTER TABLE reservation DROP crs_no, DROP posting_allowed_yn');
    }
}
