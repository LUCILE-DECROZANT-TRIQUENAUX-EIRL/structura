<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200511170514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename entities to be clearer';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipts_generation_from_fiscal_year DROP FOREIGN KEY FK_E14FE66ECE715CD7');
        $this->addSql('ALTER TABLE receipts_generation_from_two_dates DROP FOREIGN KEY FK_3B1F5587CE715CD7');
        $this->addSql('ALTER TABLE receipts_generation_receipt DROP FOREIGN KEY FK_F48F35D31A25C0D0');

        $this->addSql('CREATE TABLE receipts_from_fiscal_year_grouping_file (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, fiscal_year INT NOT NULL, UNIQUE INDEX UNIQ_77FFCC33CE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipts_grouping_file (id INT AUTO_INCREMENT NOT NULL, generator_id INT NOT NULL, generation_date_start DATETIME NOT NULL, generation_date_end DATETIME DEFAULT NULL, filename VARCHAR(200) NOT NULL, INDEX IDX_CE31C6D0CF158378 (generator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipts_grouping_file_receipt (receipts_grouping_file_id INT NOT NULL, receipt_id INT NOT NULL, INDEX IDX_1702092727B6803C (receipts_grouping_file_id), INDEX IDX_170209272B5CA896 (receipt_id), PRIMARY KEY(receipts_grouping_file_id, receipt_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipts_from_two_dates_grouping_file (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_E41FD724CE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('INSERT INTO receipts_from_fiscal_year_grouping_file '
                . '(SELECT * FROM receipts_generation_from_fiscal_year)');
        $this->addSql('INSERT INTO receipts_grouping_file '
                . '(SELECT * FROM receipts_generation)');
        $this->addSql('INSERT INTO receipts_grouping_file_receipt '
                . '(SELECT * FROM receipts_generation_receipt)');
        $this->addSql('INSERT INTO receipts_from_two_dates_grouping_file '
                . '(SELECT * FROM receipts_generation_from_two_dates)');

        $this->addSql('ALTER TABLE receipts_from_fiscal_year_grouping_file ADD CONSTRAINT FK_77FFCC33CE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_grouping_file (id)');
        $this->addSql('ALTER TABLE receipts_grouping_file ADD CONSTRAINT FK_CE31C6D0CF158378 FOREIGN KEY (generator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE receipts_grouping_file_receipt ADD CONSTRAINT FK_1702092727B6803C FOREIGN KEY (receipts_grouping_file_id) REFERENCES receipts_grouping_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipts_grouping_file_receipt ADD CONSTRAINT FK_170209272B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipts_from_two_dates_grouping_file ADD CONSTRAINT FK_E41FD724CE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_grouping_file (id)');

        $this->addSql('DROP TABLE receipts_generation');
        $this->addSql('DROP TABLE receipts_generation_from_fiscal_year');
        $this->addSql('DROP TABLE receipts_generation_from_two_dates');
        $this->addSql('DROP TABLE receipts_generation_receipt');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipts_from_fiscal_year_grouping_file DROP FOREIGN KEY FK_77FFCC33CE715CD7');
        $this->addSql('ALTER TABLE receipts_grouping_file_receipt DROP FOREIGN KEY FK_1702092727B6803C');
        $this->addSql('ALTER TABLE receipts_from_two_dates_grouping_file DROP FOREIGN KEY FK_E41FD724CE715CD7');

        $this->addSql('CREATE TABLE receipts_generation (id INT AUTO_INCREMENT NOT NULL, generator_id INT NOT NULL, generation_date_start DATETIME NOT NULL, generation_date_end DATETIME DEFAULT NULL, filename VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9A741274CF158378 (generator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE receipts_generation_from_fiscal_year (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, fiscal_year INT NOT NULL, UNIQUE INDEX UNIQ_E14FE66ECE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE receipts_generation_from_two_dates (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_3B1F5587CE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE receipts_generation_receipt (receipts_generation_id INT NOT NULL, receipt_id INT NOT NULL, INDEX IDX_F48F35D31A25C0D0 (receipts_generation_id), INDEX IDX_F48F35D32B5CA896 (receipt_id), PRIMARY KEY(receipts_generation_id, receipt_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('INSERT INTO receipts_generation_from_fiscal_year '
                . '(SELECT * FROM receipts_from_fiscal_year_grouping_file)');
        $this->addSql('INSERT INTO receipts_generation '
                . '(SELECT * FROM receipts_grouping_file)');
        $this->addSql('INSERT INTO receipts_generation_receipt '
                . '(SELECT * FROM receipts_grouping_file_receipt)');
        $this->addSql('INSERT INTO receipts_generation_from_two_dates '
                . '(SELECT * FROM receipts_from_two_dates_grouping_file)');

        $this->addSql('ALTER TABLE receipts_generation ADD CONSTRAINT FK_9A741274CF158378 FOREIGN KEY (generator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE receipts_generation_from_fiscal_year ADD CONSTRAINT FK_E14FE66ECE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_generation (id)');
        $this->addSql('ALTER TABLE receipts_generation_from_two_dates ADD CONSTRAINT FK_3B1F5587CE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_generation (id)');
        $this->addSql('ALTER TABLE receipts_generation_receipt ADD CONSTRAINT FK_F48F35D31A25C0D0 FOREIGN KEY (receipts_generation_id) REFERENCES receipts_generation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipts_generation_receipt ADD CONSTRAINT FK_F48F35D32B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE');

        $this->addSql('DROP TABLE receipts_from_fiscal_year_grouping_file');
        $this->addSql('DROP TABLE receipts_grouping_file');
        $this->addSql('DROP TABLE receipts_grouping_file_receipt');
        $this->addSql('DROP TABLE receipts_from_two_dates_grouping_file');
    }
}
