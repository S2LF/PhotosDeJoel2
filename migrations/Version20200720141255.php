<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200720141255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actualite (id INT AUTO_INCREMENT NOT NULL, categorie VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, contenu LONGTEXT NOT NULL, PRIMARY KEY(id), position INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expo (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_event DATETIME NOT NULL, contenu VARCHAR(255) NOT NULL, PRIMARY KEY(id), position INT NOT NULL, path VARCHAR(255) DEFAULT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `general` (id INT AUTO_INCREMENT NOT NULL, titre_du_site_header VARCHAR(255) DEFAULT NULL, texte_header LONGTEXT DEFAULT NULL, mot_page_accueil VARCHAR(255) DEFAULT NULL, photo_accueil_path VARCHAR(255) DEFAULT NULL, text_footer VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lien (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, lien VARCHAR(255) NOT NULL, PRIMARY KEY(id), position INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, photo_categorie_id INT NOT NULL, titre VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, exifs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_14B78418339EEE3E (photo_categorie_id), PRIMARY KEY(id), position INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_categorie (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, photo_cover_path VARCHAR(255) NOT NULL, PRIMARY KEY(id), position INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418339EEE3E FOREIGN KEY (photo_categorie_id) REFERENCES photo_categorie (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418339EEE3E');
        $this->addSql('DROP TABLE actualite');
        $this->addSql('DROP TABLE expo');
        $this->addSql('DROP TABLE `general`');
        $this->addSql('DROP TABLE lien');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE photo_categorie');
    }
}
