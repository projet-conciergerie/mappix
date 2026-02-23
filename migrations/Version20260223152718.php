<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223152718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, places INT DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, localisation_id INT NOT NULL, INDEX IDX_B26681EC68BE09C (localisation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenement_category (evenement_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_892A8229FD02F13 (evenement_id), INDEX IDX_892A822912469DE2 (category_id), PRIMARY KEY (evenement_id, category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE localisation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, INDEX IDX_42C84955FD02F13 (evenement_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EC68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id)');
        $this->addSql('ALTER TABLE evenement_category ADD CONSTRAINT FK_892A8229FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_category ADD CONSTRAINT FK_892A822912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service ADD localisation_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2C68BE09C ON service (localisation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EC68BE09C');
        $this->addSql('ALTER TABLE evenement_category DROP FOREIGN KEY FK_892A8229FD02F13');
        $this->addSql('ALTER TABLE evenement_category DROP FOREIGN KEY FK_892A822912469DE2');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FD02F13');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_category');
        $this->addSql('DROP TABLE localisation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A76ED395');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0ED5CA9E6');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1ED5CA9E6');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432A76ED395');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432ED5CA9E6');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2C68BE09C');
        $this->addSql('DROP INDEX IDX_E19D9AD2C68BE09C ON service');
        $this->addSql('ALTER TABLE service DROP localisation_id');
    }
}
