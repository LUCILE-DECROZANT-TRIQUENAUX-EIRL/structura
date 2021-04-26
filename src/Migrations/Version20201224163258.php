<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201224163258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add payment fields to register check information when needed.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment ADD check_number VARCHAR(40) NOT NULL, ADD check_issuer VARCHAR(300) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment DROP check_number, DROP check_issuer');
    }
}
