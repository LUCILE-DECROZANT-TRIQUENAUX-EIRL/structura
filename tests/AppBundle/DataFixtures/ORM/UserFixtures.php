<?php
namespace Tests\AppBundle\DataFixtures\ORM;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Responsibility;
use AppBundle\Entity\Address;
use AppBundle\Entity\Denomination;

class UserFixtures implements FixtureInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        /**********************/
        /*  Responsibilities  */
        /**********************/

        // ROLE_ADMIN
        $roleAdmin = new Responsibility();
        $roleAdmin->setCode('ROLE_ADMIN');
        $roleAdmin->setLabel('Administrateurice de la base de données');
        $roleAdmin->setDescription('Peut consulter ou restaurer les données archivées non sensibles.');

        $manager->persist($roleAdmin);

        // ROLE_ADMIN_SENSIBLE
        $roleAdminSensible = new Responsibility();
        $roleAdminSensible->setCode('ROLE_ADMIN_SENSIBLE');
        $roleAdminSensible->setLabel('Administrateurice des données sensibles');
        $roleAdminSensible->setDescription('Peut consulter ou restaurer les données sensibles archivées.');

        $manager->persist($roleAdminSensible);

        // ROLE_GESTION
        $roleGestion = new Responsibility();
        $roleGestion->setCode('ROLE_GESTION');
        $roleGestion->setLabel('Gestionnaire');
        $roleGestion->setDescription('Permet d\'afficher, éditer, supprimer les données non sensibles d\'autres comptes, de créer des comptes utilisateurice, d\'éditer les rôles d\'autres comptes (mis à part les rôles sensibles) et de consulter, modifier et supprimer des informations dans l\'annuaire des professionnels de santé.');

        $manager->persist($roleGestion);

        // ROLE_GESTION_SENSIBLE
        $roleGestionSensible = new Responsibility();
        $roleGestionSensible->setCode('ROLE_GESTION_SENSIBLE');
        $roleGestionSensible->setLabel('Gestionnaire des données sensibles');
        $roleGestionSensible->setDescription('Permet d\'afficher, éditer, supprimer les données sensibles d\'autres comptes et d\'éditer les rôles liés aux données sensibles.');

        $manager->persist($roleGestionSensible);

        // ROLE_INFORMATEURICE
        $roleInformateurice = new Responsibility();
        $roleInformateurice->setCode('ROLE_INFORMATEURICE');
        $roleInformateurice->setLabel('Informateurice');
        $roleInformateurice->setDescription('Permet de créer, afficher, éditer et supprimer un événement ou une newsletter et d\'envoyer les newsletters.');

        $manager->persist($roleInformateurice);

        // ROLE_ADHERENT_E
        $roleAdherentE = new Responsibility();
        $roleAdherentE->setCode('ROLE_ADHERENT_E');
        $roleAdherentE->setLabel('Adhérent.e');
        $roleAdherentE->setDescription('Permet de recevoir la newsletter, les convocations à l\'AG, les invitations aux événements, de consulter les documents des AG des années de cotisation, de voir les événements "privés" et de renouveler son adhésion.');

        $manager->persist($roleAdherentE);

        // ROLE_EX_ADHERENT_E
        $roleExAdherentE = new Responsibility();
        $roleExAdherentE->setCode('ROLE_EX_ADHERENT_E');
        $roleExAdherentE->setLabel('Ex-adhérent.e');
        $roleExAdherentE->setDescription('Permet de recevoir une relance pour adhérer à l\'association, renouveler son adhésion et consulter les documents des AG des années de cotisation.');

        $manager->persist($roleExAdherentE);

        // ROLE_MECENE
        $roleMecene = new Responsibility();
        $roleMecene->setCode('ROLE_MECENE');
        $roleMecene->setLabel('Mécène');
        $roleMecene->setDescription('Peut faire des dons.');

        $manager->persist($roleMecene);

        // ROLE_SYMPATHISANT_E
        $roleSympathisantE = new Responsibility();
        $roleSympathisantE->setCode('ROLE_SYMPATHISANT_E');
        $roleSympathisantE->setLabel('Sympathisant.e');
        $roleSympathisantE->setDescription('Peut recevoir la newsletter et adhérer à l\'association.');

        $manager->persist($roleSympathisantE);

        // ROLE_CONSULTATION_ANNUAIRE
        $roleConsultationAnnuaire = new Responsibility();
        $roleConsultationAnnuaire->setCode('ROLE_CONSULTATION_ANNUAIRE');
        $roleConsultationAnnuaire->setLabel('Consultation de l\'annuaire');
        $roleConsultationAnnuaire->setDescription('Donne l\'accès à la consultation de l\'annuaire des professionnels de santé.');

        $manager->persist($roleConsultationAnnuaire);

        // ROLE_INSCRIT_E
        $roleInscritE = new Responsibility();
        $roleInscritE->setCode('ROLE_INSCRIT_E');
        $roleInscritE->setLabel('Inscrit.e');
        $roleInscritE->setDescription('Permet de voir les informations de son compte, de les éditer, de les archiver et de demander l\'accès à l\'annuaire des professionels de santé.');

        $manager->persist($roleInscritE);

        /**********************/
        /*    Denominations   */
        /**********************/

        $mix = new Denomination('Mix');
        $manager->persist($mix);

        $monsieur = new Denomination('Monsieur');
        $manager->persist($monsieur);

        $madame = new Denomination('Madame');
        $manager->persist($madame);

        $docteure = new Denomination('Docteure');
        $manager->persist($docteure);

        $docteur = new Denomination('Docteur');
        $manager->persist($docteur);

        /**********************/
        /*       Users        */
        /**********************/

        // Admin user
        $userAdmin = new User();
        $userAdmin->setUsername('admin');

        $password = $this->encoder->encodePassword($userAdmin, 'a');
        $userAdmin->setPassword($password);

        $userAdmin->setDenomination($mix);
        $userAdmin->setFirstName('Admin');
        $userAdmin->setLastName('Istrator');
        $userAdmin->setEmailAddress('administrator@fake.mail');

        $userAdminAddress = new Address('2 rue de la mine', '34000', 'Montpellier', 'France');
        $manager->persist($userAdminAddress);

        $userAdmin->addAddress($userAdminAddress);

        $userAdmin->setIsReceivingNewsletter(true);
        $userAdmin->setNewsletterDematerialization(true);
        $userAdmin->setHomePhoneNumber('0467123456');
        $userAdmin->setCellPhoneNumber('0612345678');
        $userAdmin->setWorkPhoneNumber('0412345678');
        $userAdmin->setWorkFaxNumber('0434567890');
        $userAdmin->setObservations('C\'est un.e bon.ne administrateurice.');
        $userAdmin->setMedicalDetails('RAS');

        $userAdmin->addResponsibility($roleAdmin);
        $userAdmin->addResponsibility($roleGestion);
        $userAdmin->addResponsibility($roleConsultationAnnuaire);
        $userAdmin->addResponsibility($roleAdherentE);
        $userAdmin->addResponsibility($roleInscritE);

        $manager->persist($userAdmin);

        // Final flush
        $manager->flush();
    }
}