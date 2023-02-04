<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204161235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE base ADD is_random_image TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE category_photo ADD is_random_image');
        $this->addSql('ALTER TABLE category_photo CHANGE photo_cover_path photo_cover_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE base DROP is_random_image');
        $this->addSql('ALTER TABLE category_photo DROP is_random_image');
        $this->addSql('ALTER TABLE category_photo CHANGE photo_cover_path photo_cover_path VARCHAR(255) NOT NULL');
    }
}
