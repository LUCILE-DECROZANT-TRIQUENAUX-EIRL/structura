<?php

namespace Application\DatabaseMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190605155330 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users_addresses (user_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_9B70FF7A76ED395 (user_id), INDEX IDX_9B70FF7F5B7AF75 (address_id), PRIMARY KEY(user_id, address_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, line VARCHAR(1000) NOT NULL, postal_code VARCHAR(5) NOT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE denomination (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_15AEA10CEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_updater (user_id INT NOT NULL, updater_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_6A423DACA76ED395 (user_id), INDEX IDX_6A423DACE37ECFB0 (updater_id), PRIMARY KEY(user_id, updater_id, date)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_addresses ADD CONSTRAINT FK_9B70FF7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_addresses ADD CONSTRAINT FK_9B70FF7F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE user_updater ADD CONSTRAINT FK_6A423DACA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_updater ADD CONSTRAINT FK_6A423DACE37ECFB0 FOREIGN KEY (updater_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD denomination_id INT DEFAULT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD email_address VARCHAR(255) NOT NULL, ADD is_receiving_newsletter TINYINT(1) NOT NULL, ADD newsletter_dematerialization TINYINT(1) NOT NULL, ADD home_phone_number VARCHAR(10) NOT NULL, ADD cell_phone_number VARCHAR(10) NOT NULL, ADD work_phone_number VARCHAR(10) NOT NULL, ADD work_fax_number VARCHAR(10) NOT NULL, ADD observations VARCHAR(1000) NOT NULL, ADD medical_details VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E9293F06 FOREIGN KEY (denomination_id) REFERENCES denomination (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E9293F06 ON user (denomination_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_addresses DROP FOREIGN KEY FK_9B70FF7F5B7AF75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E9293F06');
        $this->addSql('DROP TABLE users_addresses');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE denomination');
        $this->addSql('DROP TABLE user_updater');
        $this->addSql('DROP INDEX IDX_8D93D649E9293F06 ON user');
        $this->addSql('ALTER TABLE user DROP denomination_id, DROP first_name, DROP last_name, DROP email_address, DROP is_receiving_newsletter, DROP newsletter_dematerialization, DROP home_phone_number, DROP cell_phone_number, DROP work_phone_number, DROP work_fax_number, DROP observations, DROP medical_details');
    }
}
