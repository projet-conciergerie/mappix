<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260224131209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT DEFAULT NULL, notation INT NOT NULL, user_id INT DEFAULT NULL, service_id INT DEFAULT NULL, INDEX IDX_8F91ABF0A76ED395 (user_id), INDEX IDX_8F91ABF0ED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, service_id INT DEFAULT NULL, INDEX IDX_64C19C1ED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, places INT DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, localisation_id INT NOT NULL, INDEX IDX_B26681EC68BE09C (localisation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenement_category (evenement_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_892A8229FD02F13 (evenement_id), INDEX IDX_892A822912469DE2 (category_id), PRIMARY KEY (evenement_id, category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_8933C432A76ED395 (user_id), INDEX IDX_8933C432ED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE localisation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_42C84955FD02F13 (evenement_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, hours VARCHAR(255) DEFAULT NULL, pmr TINYINT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, localisation_id INT NOT NULL, INDEX IDX_E19D9AD2C68BE09C (localisation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, langue VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, last_connected_at DATETIME NOT NULL, last_position VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EC68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id)');
        $this->addSql('ALTER TABLE evenement_category ADD CONSTRAINT FK_892A8229FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_category ADD CONSTRAINT FK_892A822912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A76ED395');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0ED5CA9E6');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1ED5CA9E6');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EC68BE09C');
        $this->addSql('ALTER TABLE evenement_category DROP FOREIGN KEY FK_892A8229FD02F13');
        $this->addSql('ALTER TABLE evenement_category DROP FOREIGN KEY FK_892A822912469DE2');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432A76ED395');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432ED5CA9E6');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FD02F13');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2C68BE09C');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_category');
        $this->addSql('DROP TABLE favoris');
        $this->addSql('DROP TABLE localisation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
