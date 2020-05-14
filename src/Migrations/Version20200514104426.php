<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200514104426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename the receipts_from_two_dates_grouping_file table fields';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipts_from_two_dates_grouping_file ADD date_from DATETIME NOT NULL, ADD date_to DATETIME NOT NULL, DROP start_date, DROP end_date');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipts_from_two_dates_grouping_file ADD start_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, DROP date_from, DROP date_to');
    }
}