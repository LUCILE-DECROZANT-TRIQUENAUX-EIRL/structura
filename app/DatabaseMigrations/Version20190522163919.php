<?php

declare(strict_types=1);

namespace Application\DatabaseMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522163919 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // This migration is updating the responsabilities list to give contributors some data to work with.

        // 1) Renaming responsabilities roles and label
        $this->addSql("UPDATE responsability SET code = CONCAT(code, '_OLD');");
        $this->addSql("UPDATE responsability SET label = CONCAT(label, '_OLD');");

        // 2) Inserting new responsabilities
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_ADMIN', 'Administrateurice de la base de données', 'Peut consulter ou restaurer les données archivées non sensibles.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_ADMIN_SENSIBLE', 'Administrateurice des données sensibles', 'Peut consulter ou restaurer les données sensibles archivées.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_GESTION', 'Gestionnaire', 'Permet d''afficher, éditer, supprimer les données non sensibles d''autres comptes, de créer des comptes utilisateurice,
        d''éditer les rôles d''autres comptes (mis à part les rôles sensibles) et de consulter, modifier et supprimer des informations dans l''annuaire des professionnels de santé.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_GESTION_SENSIBLE', 'Gestionnaire des données sensibles', 'Permet d''afficher, éditer, supprimer les données sensibles d''autres comptes et
        d''éditer les rôles liés aux données sensibles.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_INFORMATEURICE', 'Informateurice', 'Permet de créer, afficher, éditer et supprimer un événement ou une newsletter et d''envoyer les newsletters.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_ADHERENT_E', 'Adhérent.e', 'Permet de recevoir la newsletter, les convocations à l''AG, les invitations aux événements,
        de consulter les documents des AG des années de cotisation, de voir les événements \"privés\" et de renouveler son adhésion.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_EX_ADHERENT_E', 'Ex-adhérent.e', 'Permet de recevoir une relance pour adhérer à l''association, renouveler son adhésion et
        consulter les documents des AG des années de cotisation.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_MECENE', 'Mécène', 'Peut faire des dons.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_SYMPATHISANT_E', 'Sympathisant.e', 'Peut recevoir la newsletter et adhérer à l''association.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_CONSULTATION_ANNUAIRE', 'Consultation de l''annuaire', 'Donne l''accès à la consultation de l''annuaire des professionnels de santé.');");
        $this->addSql("INSERT INTO responsability VALUES (0, 'ROLE_INSCRIT_E', 'Inscrit.e', 'Permet de voir les informations de son compte, de les éditer, de les archiver et de demander l''accès à
        l''annuaire des professionels de santé.');");

        // 3) We trace responsabilities equivalences
        /*
            ROLE_ADMIN_OLD           -> ROLE_ADMIN + ROLE_GESTION + ROLE_ADHERENT_E + ROLE_CONSULTATION_ANNUAIRE + ROLE_INSCRIT_E
            ROLE_GESTIONNAIRE_1_OLD  -> ROLE_GESTION + ROLE_GESTION_SENSIBLE + ROLE_ADHERENT_E + ROLE_CONSULTATION_ANNUAIRE + ROLE_INSCRIT_E
            ROLE_GESTIONNAIRE_2_OLD  -> ROLE_GESTION + ROLE_ADHERENT_E + ROLE_CONSULTATION_ANNUAIRE + ROLE_INSCRIT_E
            ROLE_INFORMATEURICE_OLD  -> ROLE_INFORMATEURICE + ROLE_INSCRIT_E
            ROLE_ADHERENT_E_OLD      -> ROLE_ADHERENT_E + ROLE_INSCRIT_E
        */

        // 4) Inserting users_responsabilities
        // ROLE_ADMIN_OLD
        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_ADMIN') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADMIN_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_GESTION') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADMIN_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_ADHERENT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADMIN_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_CONSULTATION_ANNUAIRE') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADMIN_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_INSCRIT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADMIN_OLD';");

        // ROLE_GESTIONNAIRE_1_OLD
        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_GESTION') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_1_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_GESTION_SENSIBLE') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_1_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_ADHERENT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_1_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_CONSULTATION_ANNUAIRE') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_1_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_INSCRIT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_1_OLD';");

        // ROLE_GESTIONNAIRE_2_OLD
        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_GESTION') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_2_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_ADHERENT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_2_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_CONSULTATION_ANNUAIRE') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_2_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_INSCRIT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_GESTIONNAIRE_2_OLD';");

        // ROLE_INFORMATEURICE_OLD
        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_INSCRIT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_INFORMATEURICE_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_INFORMATEURICE') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_INFORMATEURICE_OLD';");

        // ROLE_ADHERENT_E_OLD
        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_ADHERENT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADHERENT_E_OLD';");

        $this->addSql("INSERT INTO users_responsabilities (`user_id`, `responsability_id`)
            SELECT DISTINCT ur.user_id, (SELECT r1.id FROM responsability r1 WHERE r1.code = 'ROLE_INSCRIT_E') as resp_id
            FROM users_responsabilities ur
            JOIN responsability r2 ON r2.id = ur.user_id
            WHERE r2.code = 'ROLE_ADHERENT_E_OLD';");

        // 5) Deleting old responsabilities
        $this->addSql("DELETE FROM users_responsabilities WHERE responsability_id in (SELECT id FROM responsability WHERE code like '%_OLD');");
        $this->addSql("DELETE FROM responsability WHERE code like '%_OLD';");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
