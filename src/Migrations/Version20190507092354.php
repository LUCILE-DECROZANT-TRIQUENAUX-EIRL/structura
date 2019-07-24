<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190507092354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_responsabilities (user_id INT NOT NULL, responsability_id INT NOT NULL, INDEX IDX_E2742A2EA76ED395 (user_id), INDEX IDX_E2742A2E2B8DC843 (responsability_id), PRIMARY KEY(user_id, responsability_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsability (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(1000) DEFAULT NULL, UNIQUE INDEX UNIQ_5AA1C46F77153098 (code), UNIQUE INDEX UNIQ_5AA1C46FEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_responsabilities ADD CONSTRAINT FK_E2742A2EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_responsabilities ADD CONSTRAINT FK_E2742A2E2B8DC843 FOREIGN KEY (responsability_id) REFERENCES responsability (id)');

        // populate the database with sample data to make dev easier
        $this->addSql(
                "INSERT INTO `responsability` VALUES "
                . "(1,'ROLE_ADMIN','Administrateurice de la base de données','Permet à l\'utilisateurice de créer, éditer et supprimer les comptes utilisateurs de la base de données.'),"
                . "(2,'ROLE_GESTIONNAIRE_1','Gestionnaire de niveau 1','Permet à l\'utilisateurice de voir toutes les informations de la base de données, de les éditer, de les supprimer et d\'en enregistrer de nouvelles (mis à part les comptes utilisateurices).'),"
                . "(3,'ROLE_GESTIONNAIRE_2','Gestionnaire de niveau 2','Permet à l\'utilisateurice de voir les informations des adhérent.e.s, de les éditer, de les archiver et d\'en enregistrer de nouvelles (mis à part le parcours médical)'),"
                . "(4,'ROLE_INFORMATEURICE','Informateurice','Permet à l\'utilisateurice d\'envoyer des mails automatiquement depuis le logiciel (relances, newletters...)'),"
                . "(5,'ROLE_ADHERENT_E','Adhérent.e','Permet à l\'utilisateurice de voir les informations de son compte, de les éditer, de les archiver et d\'en enregistrer de nouvelles');"
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_responsabilities DROP FOREIGN KEY FK_E2742A2EA76ED395');
        $this->addSql('ALTER TABLE users_responsabilities DROP FOREIGN KEY FK_E2742A2E2B8DC843');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE users_responsabilities');
        $this->addSql('DROP TABLE responsability');
    }
}
