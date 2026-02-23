<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223110653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_8933C432A76ED395 (user_id), INDEX IDX_8933C432ED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE avis ADD service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0ED5CA9E6 ON avis (service_id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('DROP INDEX IDX_E19D9AD2B83297E7 ON service');
        $this->addSql('ALTER TABLE service DROP is_partner, DROP reservation_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432A76ED395');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432ED5CA9E6');
        $this->addSql('DROP TABLE favoris');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A76ED395');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0ED5CA9E6');
        $this->addSql('DROP INDEX IDX_8F91ABF0ED5CA9E6 ON avis');
        $this->addSql('ALTER TABLE avis DROP service_id');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1ED5CA9E6');
        $this->addSql('ALTER TABLE service ADD is_partner TINYINT NOT NULL, ADD reservation_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_E19D9AD2B83297E7 ON service (reservation_id)');
    }
}
