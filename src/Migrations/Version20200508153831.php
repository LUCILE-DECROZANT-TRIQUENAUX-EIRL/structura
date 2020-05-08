<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200508153831 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add two tables to manage receipts and their generation.';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE receipts_generation (id INT AUTO_INCREMENT NOT NULL, generator_id INT NOT NULL, generation_date_start DATETIME NOT NULL, generation_date_end DATETIME DEFAULT NULL, filename VARCHAR(200) NOT NULL, INDEX IDX_9A741274CF158378 (generator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipts_generation_receipt (receipts_generation_id INT NOT NULL, receipt_id INT NOT NULL, INDEX IDX_F48F35D31A25C0D0 (receipts_generation_id), INDEX IDX_F48F35D32B5CA896 (receipt_id), PRIMARY KEY(receipts_generation_id, receipt_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, payment_id INT NOT NULL, order_num INTEGER NOT NULL, fiscal_year INT NOT NULL, UNIQUE INDEX UNIQ_5399B6454C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSqk('CREATE UNIQUE INDEX UNIQ_fiscal_year_order_num ON receipt(fiscal_year, order_num)');
        $this->addSql('ALTER TABLE receipts_generation ADD CONSTRAINT FK_9A741274CF158378 FOREIGN KEY (generator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE receipts_generation_receipt ADD CONSTRAINT FK_F48F35D31A25C0D0 FOREIGN KEY (receipts_generation_id) REFERENCES receipts_generation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipts_generation_receipt ADD CONSTRAINT FK_F48F35D32B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B6454C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipts_generation_receipt DROP FOREIGN KEY FK_F48F35D31A25C0D0');
        $this->addSql('ALTER TABLE receipts_generation_receipt DROP FOREIGN KEY FK_F48F35D32B5CA896');
        $this->addSql('DROP TABLE receipts_generation');
        $this->addSql('DROP TABLE receipts_generation_receipt');
        $this->addSql('DROP TABLE receipt');
    }
}
