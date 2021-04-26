<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201227224828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove useless check issuer field';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment DROP check_issuer');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment ADD check_issuer VARCHAR(300) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
