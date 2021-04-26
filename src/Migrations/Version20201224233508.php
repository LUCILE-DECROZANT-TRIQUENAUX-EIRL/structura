<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201224233508 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Make optionnal payment parameters nullable.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment CHANGE check_number check_number VARCHAR(40) DEFAULT NULL, CHANGE check_issuer check_issuer VARCHAR(300) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment CHANGE check_number check_number VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE check_issuer check_issuer VARCHAR(300) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
