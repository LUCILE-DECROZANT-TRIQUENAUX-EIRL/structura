<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191130081736 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add isMultiMembers and numberMaxMembers to MembershipType.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_type ADD is_multi_members TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE membership_type ADD number_max_members DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_type DROP is_multi_members, DROP number_max_members');
    }
}
