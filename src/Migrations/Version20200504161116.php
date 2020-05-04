<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200504161116 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add order code identifier to Payment';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment ADD order_number VARCHAR(255) NOT NULL, CHANGE date_received date_received DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D125F1200 ON payment (date_cashed)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_6D28840D125F1200 ON payment');
        $this->addSql('ALTER TABLE payment DROP order_number, CHANGE date_received date_received DATETIME DEFAULT NULL');
    }
}
