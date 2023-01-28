<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230122233920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actuality ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE base ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE category_photo ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE exposition ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE link ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD deleted_at DATETIME DEFAULT NULL, CHANGE exifs exifs JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exposition DROP deleted_at');
        $this->addSql('ALTER TABLE actuality DROP deleted_at');
        $this->addSql('ALTER TABLE `user` DROP deleted_at');
        $this->addSql('ALTER TABLE link DROP deleted_at');
        $this->addSql('ALTER TABLE photo DROP deleted_at, CHANGE exifs exifs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE base DROP deleted_at');
        $this->addSql('ALTER TABLE category_photo DROP deleted_at');
    }
}
