<?php

declare(strict_types=1);

namespace Application\DatabaseMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190604095506 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Creating new tables
        $this->addSql('CREATE TABLE users_responsibilities (user_id INT NOT NULL, responsibility_id INT NOT NULL, INDEX IDX_59AC8836A76ED395 (user_id), INDEX IDX_59AC8836385A88B7 (responsibility_id), PRIMARY KEY(user_id, responsibility_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsibility (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(1000) DEFAULT NULL, UNIQUE INDEX UNIQ_694E8A0877153098 (code), UNIQUE INDEX UNIQ_694E8A08EA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_responsibilities ADD CONSTRAINT FK_59AC8836A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_responsibilities ADD CONSTRAINT FK_59AC8836385A88B7 FOREIGN KEY (responsibility_id) REFERENCES responsibility (id)');

        // Saving data into the new tables
        $this->addSql("INSERT INTO responsibility (`id`, `code`, `label`, `description`)
            SELECT * FROM responsability;");

        $this->addSql("INSERT INTO users_responsibilities (`user_id`, `responsibility_id`)
            SELECT * FROM users_responsabilities;");

        // Droping old tables
        $this->addSql('ALTER TABLE users_responsabilities DROP FOREIGN KEY FK_E2742A2E2B8DC843');
        $this->addSql('DROP TABLE responsability');
        $this->addSql('DROP TABLE users_responsabilities');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Recreating the old tables
        $this->addSql('CREATE TABLE responsability (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, label VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(1000) DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_5AA1C46F77153098 (code), UNIQUE INDEX UNIQ_5AA1C46FEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_responsabilities (user_id INT NOT NULL, responsability_id INT NOT NULL, INDEX IDX_E2742A2EA76ED395 (user_id), INDEX IDX_E2742A2E2B8DC843 (responsability_id), PRIMARY KEY(user_id, responsability_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_responsabilities ADD CONSTRAINT FK_E2742A2E2B8DC843 FOREIGN KEY (responsability_id) REFERENCES responsability (id)');
        $this->addSql('ALTER TABLE users_responsabilities ADD CONSTRAINT FK_E2742A2EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        // Saving data into the old tables
        $this->addSql("INSERT INTO responsability (`id`, `code`, `label`, `description`)
            SELECT * FROM responsibility;");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT * FROM users_responsibilities;");

        // Droping the new tables
        $this->addSql('ALTER TABLE users_responsibilities DROP FOREIGN KEY FK_59AC8836385A88B7');
        $this->addSql('DROP TABLE users_responsibilities');
        $this->addSql('DROP TABLE responsibility');
    }
}
