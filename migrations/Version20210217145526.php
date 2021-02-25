<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210217145526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3A76ED395');
        $this->addSql('DROP INDEX IDX_161498D3A76ED395 ON card');
        $this->addSql('ALTER TABLE card DROP user_id');
        $this->addSql('ALTER TABLE user ADD card_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494ACC9A20 ON user (card_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_161498D3A76ED395 ON card (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494ACC9A20');
        $this->addSql('DROP INDEX UNIQ_8D93D6494ACC9A20 ON user');
        $this->addSql('ALTER TABLE user DROP card_id');
    }
}
