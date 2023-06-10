<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230505113810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add donation_origin table and its association with donation.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE donation_origin (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE donation ADD donation_origin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A066C49EF FOREIGN KEY (donation_origin_id) REFERENCES donation_origin (id)');
        $this->addSql('CREATE INDEX IDX_31E581A066C49EF ON donation (donation_origin_id)');
        $this->addSql('ALTER TABLE ext_translations CHANGE object_class object_class VARCHAR(191) NOT NULL');
        $this->addSql('CREATE INDEX general_translations_lookup_idx ON ext_translations (object_class, foreign_key)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A066C49EF');
        $this->addSql('DROP TABLE donation_origin');
        $this->addSql('DROP INDEX general_translations_lookup_idx ON ext_translations');
        $this->addSql('ALTER TABLE ext_translations CHANGE object_class object_class VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_31E581A066C49EF ON donation');
        $this->addSql('ALTER TABLE donation DROP donation_origin_id');
    }
}
