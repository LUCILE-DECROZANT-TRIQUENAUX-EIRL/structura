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

        // Admin sensible user
        $userAdminSensible = new User();
        $userAdminSensible->setUsername('adminSensible');

        $password = $this->encoder->encodePassword($userAdminSensible, 'a');
        $userAdminSensible->setPassword($password);

        $userAdminSensible->setDenomination($monsieur);
        $userAdminSensible->setFirstName('Administrator');
        $userAdminSensible->setLastName('Sensible');
        $userAdminSensible->setEmailAddress('administrator-sensible@fake.mail');

        $userAdminSensibleAddress = new Address('10 rue des catacombes', '13001', 'Marseilles', 'France');
        $manager->persist($userAdminSensibleAddress);
        $userAdminSensible->addAddress($userAdminSensibleAddress);

        $userAdminSensible->setIsReceivingNewsletter(true);
        $userAdminSensible->setNewsletterDematerialization(true);
        $userAdminSensible->setHomePhoneNumber('0467654321');
        $userAdminSensible->setCellPhoneNumber('0687654321');
        $userAdminSensible->setWorkPhoneNumber('0487654321');
        $userAdminSensible->setWorkFaxNumber('0409876543');
        $userAdminSensible->setObservations('Il aime les canards. Vivants.');
        $userAdminSensible->setMedicalDetails('RAS');

        $userAdminSensible->addResponsibility($roleAdmin);
        $userAdminSensible->addResponsibility($roleAdminSensible);
        $userAdminSensible->addResponsibility($roleGestion);
        $userAdminSensible->addResponsibility($roleGestionSensible);
        $userAdminSensible->addResponsibility($roleConsultationAnnuaire);
        $userAdminSensible->addResponsibility($roleAdherentE);
        $userAdminSensible->addResponsibility($roleInscritE);

        $manager->persist($userAdminSensible);

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

        // Gestionnaire sensible user
        $userGestionnaireSensible = new User();
        $userGestionnaireSensible->setUsername('gestiSensible');

        $password = $this->encoder->encodePassword($userGestionnaireSensible, 'a');
        $userGestionnaireSensible->setPassword($password);

        $userGestionnaireSensible->setDenomination($mix);
        $userGestionnaireSensible->setFirstName('Gestionnaire');
        $userGestionnaireSensible->setLastName('Sensible');
        $userGestionnaireSensible->setEmailAddress('gestionnaire-sensible@fake.mail');

        $userGestionnaireSensibleAddress = new Address('563 rue Olympe de Gouges', '34730', 'Prades-le-Lez', 'France');
        $manager->persist($userGestionnaireSensibleAddress);
        $userGestionnaireSensible->addAddress($userGestionnaireSensibleAddress);

        $userGestionnaireSensible->setIsReceivingNewsletter(true);
        $userGestionnaireSensible->setNewsletterDematerialization(true);
        $userGestionnaireSensible->setHomePhoneNumber('0167654321');
        $userGestionnaireSensible->setCellPhoneNumber('0787654321');
        $userGestionnaireSensible->setWorkPhoneNumber('0187654321');
        $userGestionnaireSensible->setWorkFaxNumber('0109876543');
        $userGestionnaireSensible->setObservations('Iel sent bon.');
        $userGestionnaireSensible->setMedicalDetails('RAS');

        $userGestionnaireSensible->addResponsibility($roleGestion);
        $userGestionnaireSensible->addResponsibility($roleGestionSensible);
        $userGestionnaireSensible->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaireSensible->addResponsibility($roleAdherentE);
        $userGestionnaireSensible->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaireSensible);

        // Gestionnaire user
        //gest1
        $userGestionnaire1 = new User();
        $userGestionnaire1->setUsername('gest1');

        $password = $this->encoder->encodePassword($userGestionnaire1, 'a');
        $userGestionnaire1->setPassword($password);

        $userGestionnaire1->setDenomination($madame);
        $userGestionnaire1->setFirstName('Gesti');
        $userGestionnaire1->setLastName('Onnaire1');
        $userGestionnaire1->setEmailAddress('gestionnaire1@fake.mail');

        $userGestionnaire1Address = new Address('4 rue Victor Hugo', '34000', 'Montpellier', 'France');
        $manager->persist($userGestionnaire1Address);
        $userGestionnaire1->addAddress($userGestionnaire1Address);

        $userGestionnaire1->setIsReceivingNewsletter(true);
        $userGestionnaire1->setNewsletterDematerialization(false);
        $userGestionnaire1->setHomePhoneNumber('0167123456');
        $userGestionnaire1->setCellPhoneNumber('0712345678');
        $userGestionnaire1->setWorkPhoneNumber('0112345678');
        $userGestionnaire1->setWorkFaxNumber('0134567890');
        $userGestionnaire1->setObservations('C\'est une bonne gestionnaire.');
        $userGestionnaire1->setMedicalDetails('RAS');

        $userGestionnaire1->addResponsibility($roleGestion);
        $userGestionnaire1->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaire1->addResponsibility($roleAdherentE);
        $userGestionnaire1->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaire1);

        //gest2
        $userGestionnaire2 = new User();
        $userGestionnaire2->setUsername('gest2');

        $password = $this->encoder->encodePassword($userGestionnaire2, 'a');
        $userGestionnaire2->setPassword($password);

        $userGestionnaire2->setDenomination($monsieur);
        $userGestionnaire2->setFirstName('Gesti');
        $userGestionnaire2->setLastName('Onnaire2');
        $userGestionnaire2->setEmailAddress('gestionnaire2@fake.mail');

        $userGestionnaire2Address = new Address('14 rue Victor Hugo', '34000', 'Montpellier', 'France');
        $manager->persist($userGestionnaire2Address);
        $userGestionnaire2->addAddress($userGestionnaire2Address);

        $userGestionnaire2->setIsReceivingNewsletter(true);
        $userGestionnaire2->setNewsletterDematerialization(false);
        $userGestionnaire2->setHomePhoneNumber('0167123456');
        $userGestionnaire2->setCellPhoneNumber('0712345678');
        $userGestionnaire2->setWorkPhoneNumber('0112345678');
        $userGestionnaire2->setWorkFaxNumber('0134567890');
        $userGestionnaire2->setObservations('C\'est un gestionnaire moyen mais sympathique.');
        $userGestionnaire2->setMedicalDetails('RAS');

        $userGestionnaire2->addResponsibility($roleGestion);
        $userGestionnaire2->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaire2->addResponsibility($roleAdherentE);
        $userGestionnaire2->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaire2);

        // Informateurice user
        $userInformateurice = new User();
        $userInformateurice->setUsername('info');

        $password = $this->encoder->encodePassword($userInformateurice, 'a');
        $userInformateurice->setPassword($password);

        $userInformateurice->setDenomination($madame);
        $userInformateurice->setFirstName('Info');
        $userInformateurice->setLastName('Rmateurice');
        $userInformateurice->setEmailAddress('informateurice-sensible@fake.mail');

        $userInformateuriceAddress = new Address('137 Avenue Simone Veil', '69150', 'Décines-Charpieu', 'France');
        $manager->persist($userInformateuriceAddress);
        $userInformateurice->addAddress($userInformateuriceAddress);

        $userInformateurice->setIsReceivingNewsletter(false);
        $userInformateurice->setNewsletterDematerialization(false);
        $userInformateurice->setHomePhoneNumber('0167654321');
        $userInformateurice->setCellPhoneNumber('0787654321');
        $userInformateurice->setWorkPhoneNumber('0187654321');
        $userInformateurice->setWorkFaxNumber('0109876543');
        $userInformateurice->setObservations('Il serait bien de la faire adhérer.');
        $userInformateurice->setMedicalDetails('RAS');

        $userInformateurice->addResponsibility($roleInformateurice);
        $userInformateurice->addResponsibility($roleInscritE);

        $manager->persist($userInformateurice);

        // Adhérent.e user
        // adhe1
        $userAdherentE1 = new User();
        $userAdherentE1->setUsername('adhe1');

        $password = $this->encoder->encodePassword($userAdherentE1, 'a');
        $userAdherentE1->setPassword($password);

        $userAdherentE1->setDenomination($madame);
        $userAdherentE1->setFirstName('Adhe1');
        $userAdherentE1->setLastName('Rente');
        $userAdherentE1->setEmailAddress('adherente1@fake.mail');

        $userAdherentE1Address = new Address('15 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($userAdherentE1Address);
        $userAdherentE1->addAddress($userAdherentE1Address);

        $userAdherentE1->setIsReceivingNewsletter(true);
        $userAdherentE1->setNewsletterDematerialization(false);
        $userAdherentE1->setHomePhoneNumber('0167123456');
        $userAdherentE1->setCellPhoneNumber('0712345678');
        $userAdherentE1->setWorkPhoneNumber('0112345678');
        $userAdherentE1->setWorkFaxNumber('0134567890');
        $userAdherentE1->setObservations('A appelé le 17/03/2019.');
        $userAdherentE1->setMedicalDetails('Probablement à risque');

        $userAdherentE1->addResponsibility($roleAdherentE);
        $userAdherentE1->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE1);

        // adhe2
        $userAdherentE2 = new User();
        $userAdherentE2->setUsername('adhe2');

        $password = $this->encoder->encodePassword($userAdherentE2, 'a');
        $userAdherentE2->setPassword($password);

        $userAdherentE2->setDenomination($madame);
        $userAdherentE2->setFirstName('Adhe2');
        $userAdherentE2->setLastName('Rente');
        $userAdherentE2->setEmailAddress('adherente2@fake.mail');

        $userAdherentE2Address = new Address('25 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($userAdherentE2Address);
        $userAdherentE2->addAddress($userAdherentE2Address);

        $userAdherentE2->setIsReceivingNewsletter(true);
        $userAdherentE2->setNewsletterDematerialization(true);
        $userAdherentE2->setHomePhoneNumber('0167123456');
        $userAdherentE2->setCellPhoneNumber('0712345678');
        $userAdherentE2->setWorkPhoneNumber('0112345678');
        $userAdherentE2->setWorkFaxNumber('0134567890');
        $userAdherentE2->setObservations('A appelé le 18/03/2019.');
        $userAdherentE2->setMedicalDetails('Malade');

        $userAdherentE2->addResponsibility($roleAdherentE);
        $userAdherentE2->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE2);

        // adhe3
        $userAdherentE3 = new User();
        $userAdherentE3->setUsername('adhe3');

        $password = $this->encoder->encodePassword($userAdherentE3, 'a');
        $userAdherentE3->setPassword($password);

        $userAdherentE3->setDenomination($madame);
        $userAdherentE3->setFirstName('Adhe3');
        $userAdherentE3->setLastName('Rente');
        $userAdherentE3->setEmailAddress('adherente3@fake.mail');

        $userAdherentE3Address = new Address('35 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($userAdherentE3Address);
        $userAdherentE3->addAddress($userAdherentE3Address);

        $userAdherentE3->setIsReceivingNewsletter(true);
        $userAdherentE3->setNewsletterDematerialization(false);
        $userAdherentE3->setHomePhoneNumber('0167123456');
        $userAdherentE3->setCellPhoneNumber('0712345678');
        $userAdherentE3->setWorkPhoneNumber('0112345678');
        $userAdherentE3->setWorkFaxNumber('0134567890');
        $userAdherentE3->setObservations('A appelé le 19/03/2019.');
        $userAdherentE3->setMedicalDetails('Le père est atteint.');

        $userAdherentE3->addResponsibility($roleAdherentE);
        $userAdherentE3->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE3);

        // adhe4
        $userAdherentE4 = new User();
        $userAdherentE4->setUsername('adhe4');

        $password = $this->encoder->encodePassword($userAdherentE4, 'a');
        $userAdherentE4->setPassword($password);

        $userAdherentE4->setDenomination($docteur);
        $userAdherentE4->setFirstName('Adhe4');
        $userAdherentE4->setLastName('Rente');
        $userAdherentE4->setEmailAddress('adherente4@fake.mail');

        $userAdherentE4Address = new Address('45 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($userAdherentE4Address);
        $userAdherentE4->addAddress($userAdherentE4Address);

        $userAdherentE4->setIsReceivingNewsletter(false);
        $userAdherentE4->setNewsletterDematerialization(false);
        $userAdherentE4->setHomePhoneNumber('0167123456');
        $userAdherentE4->setCellPhoneNumber('0712345678');
        $userAdherentE4->setWorkPhoneNumber('0112345678');
        $userAdherentE4->setWorkFaxNumber('0134567890');
        $userAdherentE4->setObservations('A appelé le 20/03/2019.');
        $userAdherentE4->setMedicalDetails('Non touché par la maladie');

        $userAdherentE4->addResponsibility($roleAdherentE);
        $userAdherentE4->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE4);

        // adhe5
        $userAdherentE5 = new User();
        $userAdherentE5->setUsername('adhe5');

        $password = $this->encoder->encodePassword($userAdherentE5, 'a');
        $userAdherentE5->setPassword($password);

        $userAdherentE5->setDenomination($docteure);
        $userAdherentE5->setFirstName('Adhe5');
        $userAdherentE5->setLastName('Rente');
        $userAdherentE5->setEmailAddress('adherente5@fake.mail');

        $userAdherentE5Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($userAdherentE5Address);
        $userAdherentE5->addAddress($userAdherentE5Address);

        $userAdherentE5->setIsReceivingNewsletter(true);
        $userAdherentE5->setNewsletterDematerialization(false);
        $userAdherentE5->setHomePhoneNumber('0167123456');
        $userAdherentE5->setCellPhoneNumber('0712345678');
        $userAdherentE5->setWorkPhoneNumber('0112345678');
        $userAdherentE5->setWorkFaxNumber('0134567890');
        $userAdherentE5->setObservations('A appelé le 21/03/2019.');
        $userAdherentE5->setMedicalDetails('Résultat du test en attente');

        $userAdherentE5->addResponsibility($roleAdherentE);
        $userAdherentE5->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE5);

        // Final flush
        $manager->flush();
    }
}