<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200713102639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add boolean to payment type to check if Bank information is needed in payment.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_type ADD is_bankneeded TINYINT(1) NOT NULL');

        // Bank information is only needed in payment of type Check
        $this->addSql('UPDATE payment_type SET is_bankneeded = 0 WHERE id = 1');
        $this->addSql('UPDATE payment_type SET is_bankneeded = 0 WHERE id = 2');
        $this->addSql('UPDATE payment_type SET is_bankneeded = 0 WHERE id = 3');
        $this->addSql('UPDATE payment_type SET is_bankneeded = 1 WHERE id = 4');
        $this->addSql('UPDATE payment_type SET is_bankneeded = 0 WHERE id = 5');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_type DROP is_bankneeded');
    }
}
