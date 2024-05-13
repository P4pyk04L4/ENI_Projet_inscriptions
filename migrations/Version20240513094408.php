<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240513094408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD site_organisateur_id INT NOT NULL, ADD lieu_id INT NOT NULL');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D7AC6C11 FOREIGN KEY (site_organisateur_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F26AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D7AC6C11 ON sortie (site_organisateur_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F26AB213CC ON sortie (lieu_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2D7AC6C11');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F26AB213CC');
        $this->addSql('DROP INDEX IDX_3C3FD3F2D7AC6C11 ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F26AB213CC ON sortie');
        $this->addSql('ALTER TABLE sortie DROP site_organisateur_id, DROP lieu_id');
    }
}
