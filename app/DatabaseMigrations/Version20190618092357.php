<?php

declare(strict_types=1);

namespace Application\DatabaseMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Divide people data into two tables: user and people.
 */
final class Version20190618092357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // create the people table
        $this->addSql('CREATE TABLE people (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, denomination_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, is_receiving_newsletter TINYINT(1) NOT NULL, newsletter_dematerialization TINYINT(1) NOT NULL, home_phone_number VARCHAR(10) NOT NULL, cell_phone_number VARCHAR(10) NOT NULL, work_phone_number VARCHAR(10) NOT NULL, work_fax_number VARCHAR(10) NOT NULL, observations VARCHAR(1000) NOT NULL, sensitive_observations VARCHAR(1000) NOT NULL, UNIQUE INDEX UNIQ_28166A26A76ED395 (user_id), INDEX IDX_28166A26E9293F06 (denomination_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // add user to people (create constraint)
        $this->addSql('ALTER TABLE people ADD CONSTRAINT FK_28166A26A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        // add denomination to people
        $this->addSql('ALTER TABLE people ADD CONSTRAINT FK_28166A26E9293F06 FOREIGN KEY (denomination_id) REFERENCES denomination (id)');
        // save old data
        $this->addSql('INSERT INTO people (user_id, first_name, last_name, email_address, is_receiving_newsletter, newsletter_dematerialization, home_phone_number, cell_phone_number, work_phone_number, work_fax_number, observations, sensitive_observations, denomination_id) SELECT id, first_name, last_name, email_address, is_receiving_newsletter, newsletter_dematerialization, home_phone_number, cell_phone_number, work_phone_number, work_fax_number, observations, medical_details, denomination_id FROM user');

        // add addresses to people (create table and save old data)
        $this->addSql('CREATE TABLE people_addresses (people_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_EFDEE3F13147C936 (people_id), INDEX IDX_EFDEE3F1F5B7AF75 (address_id), PRIMARY KEY(people_id, address_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE people_addresses ADD CONSTRAINT FK_EFDEE3F13147C936 FOREIGN KEY (people_id) REFERENCES people (id)');
        $this->addSql('ALTER TABLE people_addresses ADD CONSTRAINT FK_EFDEE3F1F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('INSERT INTO people_addresses (people_id, address_id) SELECT people.id, users_addresses.address_id FROM people, user, users_addresses WHERE people.user_id = user.id AND user.id = users_addresses.user_id');

        // drop old table and columns not used anymore
        $this->addSql('DROP TABLE users_addresses');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E9293F06');
        $this->addSql('DROP INDEX IDX_8D93D649E9293F06 ON user');
        $this->addSql('ALTER TABLE user DROP denomination_id, DROP first_name, DROP last_name, DROP email_address, DROP is_receiving_newsletter, DROP newsletter_dematerialization, DROP home_phone_number, DROP cell_phone_number, DROP work_phone_number, DROP work_fax_number, DROP observations, DROP medical_details');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // recreate the table storaging the user addresses and fill it with old data
        $this->addSql('CREATE TABLE users_addresses (user_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_9B70FF7A76ED395 (user_id), INDEX IDX_9B70FF7F5B7AF75 (address_id), PRIMARY KEY(user_id, address_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_addresses ADD CONSTRAINT FK_9B70FF7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_addresses ADD CONSTRAINT FK_9B70FF7F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('INSERT INTO users_addresses (user_id, address_id) SELECT people.user_id, people_addresses.address_id FROM people, people_addresses WHERE people.id = people_addresses.people_id');
        $this->addSql('ALTER TABLE people_addresses DROP FOREIGN KEY FK_EFDEE3F13147C936');
        $this->addSql('DROP TABLE people_addresses');

        // readd the colums in user table and fill it with old data
        $this->addSql('ALTER TABLE user ADD denomination_id INT DEFAULT NULL, ADD first_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD last_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD email_address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD is_receiving_newsletter TINYINT(1) NOT NULL, ADD newsletter_dematerialization TINYINT(1) NOT NULL, ADD home_phone_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, ADD cell_phone_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, ADD work_phone_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, ADD work_fax_number VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, ADD observations VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci, ADD medical_details VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E9293F06 FOREIGN KEY (denomination_id) REFERENCES denomination (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E9293F06 ON user (denomination_id)');
        $this->addSql('UPDATE user INNER JOIN people ON user.id = people.user_id SET user.first_name = people.first_name, user.last_name = people.last_name, user.email_address = people.email_address, user.is_receiving_newsletter = people.is_receiving_newsletter, user.newsletter_dematerialization = people.newsletter_dematerialization, user.home_phone_number = people.home_phone_number, user.cell_phone_number = people.cell_phone_number, user.work_phone_number = people.work_phone_number, user.work_fax_number = people.work_fax_number, user.observations = people.observations, user.medical_details = people.sensitive_observations, user.denomination_id = people.denomination_id');
        $this->addSql('DROP TABLE people');
    }
}
