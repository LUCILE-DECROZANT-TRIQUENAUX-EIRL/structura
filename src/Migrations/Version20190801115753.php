<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190801115753 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Set line and postal_code as nullable to allow the save of empty addresses.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE address CHANGE line line VARCHAR(1000) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(5) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE address CHANGE line line VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci, CHANGE postal_code postal_code VARCHAR(5) NOT NULL COLLATE utf8_unicode_ci');
    }
}
