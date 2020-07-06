<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200706170151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add boolean on people type to display it only to rightful users.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE people_type ADD is_sensible TINYINT(1) NOT NULL');
        $this->addSql('UPDATE people_type SET is_sensible = 1 WHERE code = 2');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE people_type DROP is_sensible');
    }
}
