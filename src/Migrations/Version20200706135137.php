<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200706135137 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create people type and add two default types to it.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE peoples_people_types (people_id INT NOT NULL, people_type_id INT NOT NULL, INDEX IDX_5EB9919A3147C936 (people_id), INDEX IDX_5EB9919A85E590D7 (people_type_id), PRIMARY KEY(people_id, people_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE people_type (id INT AUTO_INCREMENT NOT NULL, code INT NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(3000) NOT NULL, UNIQUE INDEX UNIQ_8E179B9977153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE peoples_people_types ADD CONSTRAINT FK_5EB9919A3147C936 FOREIGN KEY (people_id) REFERENCES people (id)');
        $this->addSql('ALTER TABLE peoples_people_types ADD CONSTRAINT FK_5EB9919A85E590D7 FOREIGN KEY (people_type_id) REFERENCES people_type (id)');
        $this->addSql('INSERT INTO people_type (code, label, description) VALUES (1, "Contact", "Correspondant privilégié de l\'association, partenaire.")');
        $this->addSql('INSERT INTO people_type (code, label, description) VALUES (2, "Pôle Social", "Personne en relation avec le Pôle Social de l\'association.")');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE peoples_people_types DROP FOREIGN KEY FK_5EB9919A85E590D7');
        $this->addSql('DROP TABLE peoples_people_types');
        $this->addSql('DROP TABLE people_type');
    }
}
