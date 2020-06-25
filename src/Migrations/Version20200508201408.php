<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200508201408 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates two tables that specialize a ReceiptsGeneration entity.';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE receipts_generation_from_two_dates (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_3B1F5587CE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipts_generation_from_fiscal_year (id INT AUTO_INCREMENT NOT NULL, receipts_generation_base_id INT NOT NULL, fiscal_year INT NOT NULL, UNIQUE INDEX UNIQ_E14FE66ECE715CD7 (receipts_generation_base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE receipts_generation_from_two_dates ADD CONSTRAINT FK_3B1F5587CE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_generation (id)');
        $this->addSql('ALTER TABLE receipts_generation_from_fiscal_year ADD CONSTRAINT FK_E14FE66ECE715CD7 FOREIGN KEY (receipts_generation_base_id) REFERENCES receipts_generation (id)');
        $this->addSql('DROP INDEX UNIQ_fiscal_year_order_num ON receipt');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE receipts_generation_from_two_dates');
        $this->addSql('DROP TABLE receipts_generation_from_fiscal_year');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_fiscal_year_order_num ON receipt (fiscal_year, order_num)');
    }
}
