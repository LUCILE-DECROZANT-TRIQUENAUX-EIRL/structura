<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191204103526 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add a payer to a payment to know who gave the money.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment ADD payer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DC17AD9A9 FOREIGN KEY (payer_id) REFERENCES people (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DC17AD9A9 ON payment (payer_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DC17AD9A9');
        $this->addSql('DROP INDEX IDX_6D28840DC17AD9A9 ON payment');
        $this->addSql('ALTER TABLE payment DROP payer_id');
    }
}
