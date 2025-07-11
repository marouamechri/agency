<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711125814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointement (id INT AUTO_INCREMENT NOT NULL, titre_id INT NOT NULL, date DATETIME NOT NULL, email VARCHAR(100) NOT NULL, tel VARCHAR(14) NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, INDEX IDX_BD9991CDD54FAE5E (titre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bien (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, titre VARCHAR(100) NOT NULL, nb_piece INT NOT NULL, surface INT NOT NULL, prix DOUBLE PRECISION NOT NULL, localisation VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, etage INT NOT NULL, transaction_type VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, date_construction DATE NOT NULL, INDEX IDX_45EDC386A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, id_bien_id INT DEFAULT NULL, photo VARCHAR(50) NOT NULL, INDEX IDX_C53D045F6308117F (id_bien_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE option_bien (id INT AUTO_INCREMENT NOT NULL, idbien_id INT DEFAULT NULL, id_option_id INT DEFAULT NULL, INDEX IDX_ADE369656291EA61 (idbien_id), INDEX IDX_ADE3696527F1A148 (id_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointement ADD CONSTRAINT FK_BD9991CDD54FAE5E FOREIGN KEY (titre_id) REFERENCES bien (id)');
        $this->addSql('ALTER TABLE bien ADD CONSTRAINT FK_45EDC386A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F6308117F FOREIGN KEY (id_bien_id) REFERENCES bien (id)');
        $this->addSql('ALTER TABLE option_bien ADD CONSTRAINT FK_ADE369656291EA61 FOREIGN KEY (idbien_id) REFERENCES bien (id)');
        $this->addSql('ALTER TABLE option_bien ADD CONSTRAINT FK_ADE3696527F1A148 FOREIGN KEY (id_option_id) REFERENCES `option` (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointement DROP FOREIGN KEY FK_BD9991CDD54FAE5E');
        $this->addSql('ALTER TABLE bien DROP FOREIGN KEY FK_45EDC386A76ED395');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F6308117F');
        $this->addSql('ALTER TABLE option_bien DROP FOREIGN KEY FK_ADE369656291EA61');
        $this->addSql('ALTER TABLE option_bien DROP FOREIGN KEY FK_ADE3696527F1A148');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE appointement');
        $this->addSql('DROP TABLE bien');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE option_bien');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
