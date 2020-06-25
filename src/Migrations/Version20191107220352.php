<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107220352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE peoples_memberships (membership_id INT NOT NULL, people_id INT NOT NULL, INDEX IDX_604F38C21FB354CD (membership_id), INDEX IDX_604F38C23147C936 (people_id), PRIMARY KEY(membership_id, people_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE peoples_memberships ADD CONSTRAINT FK_604F38C21FB354CD FOREIGN KEY (membership_id) REFERENCES people (id)');
        $this->addSql('ALTER TABLE peoples_memberships ADD CONSTRAINT FK_604F38C23147C936 FOREIGN KEY (people_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE people DROP FOREIGN KEY FK_28166A261FB354CD');
        $this->addSql('DROP INDEX UNIQ_28166A261FB354CD ON people');
        $this->addSql('ALTER TABLE people DROP membership_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE peoples_memberships');
        $this->addSql('ALTER TABLE people ADD membership_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE people ADD CONSTRAINT FK_28166A261FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_28166A261FB354CD ON people (membership_id)');
    }
}
