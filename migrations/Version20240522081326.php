<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522081326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE photo_profil (id INT AUTO_INCREMENT NOT NULL, participant_id INT NOT NULL, nom VARCHAR(255) NOT NULL, chemin_acces VARCHAR(255) NOT NULL, date_upload DATETIME NOT NULL, photo_active TINYINT(1) NOT NULL, INDEX IDX_B369C5BF9D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photo_profil ADD CONSTRAINT FK_B369C5BF9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo_profil DROP FOREIGN KEY FK_B369C5BF9D1C3019');
        $this->addSql('DROP TABLE photo_profil');
    }
}
