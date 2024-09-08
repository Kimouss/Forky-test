<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240908113620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE permission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE permission (id INT NOT NULL, profile_id INT DEFAULT NULL, base_route VARCHAR(255) NOT NULL, access JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E04992AACCFA12B8 ON permission (profile_id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AACCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE permission_id_seq CASCADE');
        $this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AACCFA12B8');
        $this->addSql('DROP TABLE permission');
    }
}
