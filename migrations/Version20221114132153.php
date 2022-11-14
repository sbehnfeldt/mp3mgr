<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114132153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mp3_file ADD album_id_id INT DEFAULT NULL, ADD year INT DEFAULT NULL, ADD length VARCHAR(255) DEFAULT NULL, ADD lyric LONGTEXT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD genre VARCHAR(255) DEFAULT NULL, ADD encoded VARCHAR(255) DEFAULT NULL, ADD copyright VARCHAR(255) NOT NULL, ADD publisher LONGTEXT DEFAULT NULL, ADD original_artist LONGTEXT DEFAULT NULL, ADD url LONGTEXT DEFAULT NULL, ADD comments LONGTEXT DEFAULT NULL, ADD composer LONGTEXT DEFAULT NULL, ADD album_art LONGBLOB DEFAULT NULL, CHANGE filename filename LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE mp3_file ADD CONSTRAINT FK_57D27E3D9FCD471 FOREIGN KEY (album_id_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_57D27E3D9FCD471 ON mp3_file (album_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mp3_file DROP FOREIGN KEY FK_57D27E3D9FCD471');
        $this->addSql('DROP INDEX IDX_57D27E3D9FCD471 ON mp3_file');
        $this->addSql('ALTER TABLE mp3_file DROP album_id_id, DROP year, DROP length, DROP lyric, DROP description, DROP genre, DROP encoded, DROP copyright, DROP publisher, DROP original_artist, DROP url, DROP comments, DROP composer, DROP album_art, CHANGE filename filename VARCHAR(255) NOT NULL');
    }
}
