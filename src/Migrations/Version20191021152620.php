<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add registration of member to the association and payment&payment type
 */
final class Version20191021152620 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add registration of member to the association and payment&payment type.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE membership_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(3000) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, payment_id INT DEFAULT NULL, date DATETIME NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_86FFD285C54C8C93 (type_id), UNIQUE INDEX UNIQ_86FFD2854C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(3000) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, date_received DATETIME DEFAULT NULL, date_cashed DATETIME DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, is_settled TINYINT(1) NOT NULL, comment VARCHAR(3000) DEFAULT NULL, INDEX IDX_6D28840DC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DC54C8C93 FOREIGN KEY (type_id) REFERENCES payment_type (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD285C54C8C93 FOREIGN KEY (type_id) REFERENCES membership_type (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD2854C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE people ADD membership_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE people ADD CONSTRAINT FK_28166A261FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_28166A261FB354CD ON people (membership_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD285C54C8C93');
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD2854C3A3BB');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DC54C8C93');
        $this->addSql('ALTER TABLE people DROP FOREIGN KEY FK_28166A261FB354CD');
        $this->addSql('DROP TABLE membership_type');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_type');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP INDEX UNIQ_28166A261FB354CD ON people');
        $this->addSql('ALTER TABLE people DROP membership_id');
    }
}
