<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204132311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add short label for denomination';
    }

    public function up(Schema $schema): void
    {
        // Add short_label field
        $this->addSql('ALTER TABLE denomination ADD short_label VARCHAR(255) DEFAULT NULL');

        // Update existing denominations
        $this->addSql("UPDATE denomination SET short_label = 'M.' WHERE label = 'Monsieur'");
        $this->addSql("UPDATE denomination SET short_label = 'Mme' WHERE label = 'Madame'");
        $this->addSql("UPDATE denomination SET short_label = 'Mx' WHERE label = 'Mix'");
        $this->addSql("UPDATE denomination SET short_label = 'Dr' WHERE label = 'Docteur'");
        $this->addSql("UPDATE denomination SET short_label = 'Me' WHERE label = 'Maître'");
        $this->addSql("UPDATE denomination SET short_label = 'Sté' WHERE label = 'Société'");
        $this->addSql("UPDATE denomination SET short_label = 'Assoc.' WHERE label = 'Association'");
    }

    public function down(Schema $schema): void
    {
        // Remove short_label field
        $this->addSql('ALTER TABLE denomination DROP short_label');
    }
}
