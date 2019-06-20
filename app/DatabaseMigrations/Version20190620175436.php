<?php

namespace Application\DatabaseMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190620175436 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE people CHANGE email_address email_address VARCHAR(255) DEFAULT NULL, CHANGE home_phone_number home_phone_number VARCHAR(10) DEFAULT NULL, CHANGE cell_phone_number cell_phone_number VARCHAR(10) DEFAULT NULL, CHANGE work_phone_number work_phone_number VARCHAR(10) DEFAULT NULL, CHANGE work_fax_number work_fax_number VARCHAR(10) DEFAULT NULL, CHANGE observations observations VARCHAR(1000) DEFAULT NULL, CHANGE sensitive_observations sensitive_observations VARCHAR(1000) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE people CHANGE email_address email_address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE home_phone_number home_phone_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, CHANGE cell_phone_number cell_phone_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, CHANGE work_phone_number work_phone_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, CHANGE work_fax_number work_fax_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, CHANGE observations observations VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci, CHANGE sensitive_observations sensitive_observations VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci');
    }
}
