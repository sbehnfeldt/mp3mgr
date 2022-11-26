<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221125212449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mp3_file CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE album_name album_name VARCHAR(255) DEFAULT NULL, CHANGE author author VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(1024) DEFAULT NULL, CHANGE genre genre VARCHAR(255) DEFAULT NULL, CHANGE publisher publisher VARCHAR(255) DEFAULT NULL, CHANGE original_artist original_artist VARCHAR(255) DEFAULT NULL, CHANGE url url VARCHAR(1024) DEFAULT NULL, CHANGE composer composer VARCHAR(255) DEFAULT NULL, CHANGE album_author album_author VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mp3_file CHANGE title title LONGTEXT DEFAULT NULL, CHANGE album_name album_name LONGTEXT DEFAULT NULL, CHANGE author author LONGTEXT DEFAULT NULL, CHANGE album_author album_author LONGTEXT DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE genre genre LONGTEXT DEFAULT NULL, CHANGE publisher publisher LONGTEXT DEFAULT NULL, CHANGE original_artist original_artist LONGTEXT DEFAULT NULL, CHANGE url url LONGTEXT DEFAULT NULL, CHANGE composer composer LONGTEXT DEFAULT NULL');
    }
}
