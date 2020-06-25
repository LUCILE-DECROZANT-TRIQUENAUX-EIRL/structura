<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191203114909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create the donation table to save donations.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE donation (id INT AUTO_INCREMENT NOT NULL, payment_id INT NOT NULL, donator_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, donation_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_31E581A04C3A3BB (payment_id), INDEX IDX_31E581A0831BACAF (donator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A04C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0831BACAF FOREIGN KEY (donator_id) REFERENCES people (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE donation');
    }
}
