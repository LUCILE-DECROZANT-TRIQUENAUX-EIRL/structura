<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191030223835 extends AbstractMigration
{
    public function getDescription() : string
    {
        return
            'Adds a default amount to a membershipType, an amount to a membership.'.
            'Removes the is_settled field from payment.'.
            'Insert the PaymentType and MembershipType into the DB.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_type ADD default_amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE payment DROP is_settled');
        $this->addSql('ALTER TABLE membership ADD amount DOUBLE PRECISION NOT NULL, ADD date_end DATETIME NOT NULL, CHANGE date date_start DATETIME NOT NULL');

        // Inserting PaymentType
        $this->addSql(
            "INSERT INTO `payment_type` (`id`, `label`, `description`) VALUES "
            . "(1, 'Espèces', 'Paiement en liquide.'),"
            . "(2, 'Carte Bleue', 'Paiement en carte bancaire.'),"
            . "(3, 'Virement', 'Paiement par virement bancaire.'),"
            . "(4, 'Chèque', 'Paiement en chèque.'),"
            . "(5, 'HelloAsso', 'Paiement perçu via la plateforme HelloAsso.')"
        );

        // Inserting MembershipType
        $this->addSql(
            "INSERT INTO `membership_type` (`id`, `label`, `description`, `default_amount`) VALUES "
            . "(1, 'Normale', 'Adhésion d\'une personne à l\'association pour une année.', 20),"
            . "(2, 'Famille', 'Adhésion d\'une famille à l\'association pour une année.', 30),"
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM payment_type');
        $this->addSql('DELETE FROM membership_type');

        $this->addSql('ALTER TABLE membership ADD date DATETIME NOT NULL, DROP amount, DROP date_start, DROP date_end');
        $this->addSql('ALTER TABLE membership_type DROP default_amount');
        $this->addSql('ALTER TABLE payment ADD is_settled TINYINT(1) NOT NULL');
    }
}
