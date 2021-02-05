<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204132934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD modification_date DATETIME DEFAULT NULL, DROP modifiaction_date, DROP user_id, CHANGE creation_date creation_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE game ADD modification_date DATETIME DEFAULT NULL, DROP modifiaction_date, CHANGE creation_date creation_date DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD modifiaction_date DATE DEFAULT NULL, ADD user_id INT NOT NULL, DROP modification_date, CHANGE creation_date creation_date DATE NOT NULL');
        $this->addSql('ALTER TABLE game ADD modifiaction_date DATE DEFAULT NULL, DROP modification_date, CHANGE creation_date creation_date DATE NOT NULL');
    }
}
