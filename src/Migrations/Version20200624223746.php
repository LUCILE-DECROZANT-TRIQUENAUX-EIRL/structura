<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200624223746 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename table receipts_from_fiscal_year_grouping_file to receipts_from_year_grouping_file';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE receipts_from_year_grouping_file (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, year INT NOT NULL, UNIQUE INDEX UNIQ_93626ED2CE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE receipts_from_year_grouping_file ADD CONSTRAINT FK_93626ED2CE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_grouping_file (id)');
        $this->addSql('INSERT INTO receipts_from_year_grouping_file SELECT * FROM receipts_from_fiscal_year_grouping_file');
        $this->addSql('DROP TABLE receipts_from_fiscal_year_grouping_file');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE receipts_from_fiscal_year_grouping_file (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, fiscal_year INT NOT NULL, UNIQUE INDEX UNIQ_77FFCC33CE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE receipts_from_fiscal_year_grouping_file ADD CONSTRAINT FK_77FFCC33CE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_grouping_file (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('INSERT INTO receipts_from_fiscal_year_grouping_file SELECT * FROM receipts_from_year_grouping_file');
        $this->addSql('DROP TABLE receipts_from_year_grouping_file');
    }
}
