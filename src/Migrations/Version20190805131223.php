<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190805131223 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add the automatic field to the Responsibility entity and update the old responsibilities';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // add the field
        $this->addSql('ALTER TABLE responsibility ADD automatic TINYINT(1) NOT NULL');

        // update the old responsibilities according to internal decision
        $this->addSql('UPDATE responsibility SET automatic = 1 WHERE label = "Adhérent.e"');
        $this->addSql('UPDATE responsibility SET automatic = 1 WHERE label = "Ex-adhérent.e"');
        $this->addSql('UPDATE responsibility SET automatic = 1 WHERE label = "Mécène"');
        $this->addSql('UPDATE responsibility SET automatic = 1 WHERE label = "Inscrit.e"');
        $this->addSql('UPDATE responsibility SET automatic = 0 WHERE automatic != 1');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE responsibility DROP automatic');
    }
}
