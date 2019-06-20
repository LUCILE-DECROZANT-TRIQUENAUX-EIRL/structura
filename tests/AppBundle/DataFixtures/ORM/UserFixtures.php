<?php
namespace Tests\AppBundle\DataFixtures\ORM;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\People;
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

        $peopleAdminSensible = new People();

        $peopleAdminSensible->setDenomination($monsieur);
        $peopleAdminSensible->setFirstName('Administrator');
        $peopleAdminSensible->setLastName('Sensible');
        $peopleAdminSensible->setEmailAddress('administrator-sensible@fake.mail');

        $peopleAdminSensibleAddress = new Address('10 rue des catacombes', '13001', 'Marseilles', 'France');
        $manager->persist($peopleAdminSensibleAddress);
        $peopleAdminSensible->addAddress($peopleAdminSensibleAddress);

        $peopleAdminSensible->setIsReceivingNewsletter(true);
        $peopleAdminSensible->setNewsletterDematerialization(true);
        $peopleAdminSensible->setHomePhoneNumber('0467654321');
        $peopleAdminSensible->setCellPhoneNumber('0687654321');
        $peopleAdminSensible->setWorkPhoneNumber('0487654321');
        $peopleAdminSensible->setWorkFaxNumber('0409876543');
        $peopleAdminSensible->setObservations('Il aime les canards. Vivants.');
        $peopleAdminSensible->setSensitiveObservations('RAS');

        $userAdminSensible->addResponsibility($roleAdmin);
        $userAdminSensible->addResponsibility($roleAdminSensible);
        $userAdminSensible->addResponsibility($roleGestion);
        $userAdminSensible->addResponsibility($roleGestionSensible);
        $userAdminSensible->addResponsibility($roleConsultationAnnuaire);
        $userAdminSensible->addResponsibility($roleAdherentE);
        $userAdminSensible->addResponsibility($roleInscritE);

        $manager->persist($userAdminSensible);
        $peopleAdminSensible->setUser($userAdminSensible);
        $manager->persist($peopleAdminSensible);

        // Admin user
        $userAdmin = new User();
        $userAdmin->setUsername('admin');

        $password = $this->encoder->encodePassword($userAdmin, 'a');
        $userAdmin->setPassword($password);

        $peopleAdmin = new People();

        $peopleAdmin->setDenomination($mix);
        $peopleAdmin->setFirstName('Admin');
        $peopleAdmin->setLastName('Istrator');
        $peopleAdmin->setEmailAddress('administrator@fake.mail');

        $peopleAdminAddress = new Address('2 rue de la mine', '34000', 'Montpellier', 'France');
        $manager->persist($peopleAdminAddress);
        $peopleAdmin->addAddress($peopleAdminAddress);

        $peopleAdmin->setIsReceivingNewsletter(true);
        $peopleAdmin->setNewsletterDematerialization(true);
        $peopleAdmin->setHomePhoneNumber('0467123456');
        $peopleAdmin->setCellPhoneNumber('0612345678');
        $peopleAdmin->setWorkPhoneNumber('0412345678');
        $peopleAdmin->setWorkFaxNumber('0434567890');
        $peopleAdmin->setObservations('C\'est un.e bon.ne administrateurice.');
        $peopleAdmin->setSensitiveObservations('RAS');

        $userAdmin->addResponsibility($roleAdmin);
        $userAdmin->addResponsibility($roleGestion);
        $userAdmin->addResponsibility($roleConsultationAnnuaire);
        $userAdmin->addResponsibility($roleAdherentE);
        $userAdmin->addResponsibility($roleInscritE);

        $manager->persist($userAdmin);
        $peopleAdmin->setUser($userAdmin);
        $manager->persist($peopleAdmin);

        // Gestionnaire sensible user
        $userGestionnaireSensible = new User();
        $userGestionnaireSensible->setUsername('gestiSensible');

        $password = $this->encoder->encodePassword($userGestionnaireSensible, 'a');
        $userGestionnaireSensible->setPassword($password);

        $peopleGestionnaireSensible = new People();

        $peopleGestionnaireSensible->setDenomination($mix);
        $peopleGestionnaireSensible->setFirstName('Gestionnaire');
        $peopleGestionnaireSensible->setLastName('Sensible');
        $peopleGestionnaireSensible->setEmailAddress('gestionnaire-sensible@fake.mail');

        $peopleGestionnaireSensibleAddress = new Address('563 rue Olympe de Gouges', '34730', 'Prades-le-Lez', 'France');
        $manager->persist($peopleGestionnaireSensibleAddress);
        $peopleGestionnaireSensible->addAddress($peopleGestionnaireSensibleAddress);

        $peopleGestionnaireSensible->setIsReceivingNewsletter(true);
        $peopleGestionnaireSensible->setNewsletterDematerialization(true);
        $peopleGestionnaireSensible->setHomePhoneNumber('0167654321');
        $peopleGestionnaireSensible->setCellPhoneNumber('0787654321');
        $peopleGestionnaireSensible->setWorkPhoneNumber('0187654321');
        $peopleGestionnaireSensible->setWorkFaxNumber('0109876543');
        $peopleGestionnaireSensible->setObservations('Iel sent bon.');
        $peopleGestionnaireSensible->setSensitiveObservations('RAS');

        $userGestionnaireSensible->addResponsibility($roleGestion);
        $userGestionnaireSensible->addResponsibility($roleGestionSensible);
        $userGestionnaireSensible->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaireSensible->addResponsibility($roleAdherentE);
        $userGestionnaireSensible->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaireSensible);
        $peopleGestionnaireSensible->setUser($userGestionnaireSensible);
        $manager->persist($peopleGestionnaireSensible);

        // Gestionnaire user
        //gest1
        $userGestionnaire1 = new User();
        $userGestionnaire1->setUsername('gest1');

        $password = $this->encoder->encodePassword($userGestionnaire1, 'a');
        $userGestionnaire1->setPassword($password);

        $peopleGestionnaire1 = new People();

        $peopleGestionnaire1->setDenomination($madame);
        $peopleGestionnaire1->setFirstName('Gesti');
        $peopleGestionnaire1->setLastName('Onnaire1');
        $peopleGestionnaire1->setEmailAddress('gestionnaire1@fake.mail');

        $peopleGestionnaire1Address = new Address('4 rue Victor Hugo', '34000', 'Montpellier', 'France');
        $manager->persist($peopleGestionnaire1Address);
        $peopleGestionnaire1->addAddress($peopleGestionnaire1Address);

        $peopleGestionnaire1->setIsReceivingNewsletter(true);
        $peopleGestionnaire1->setNewsletterDematerialization(false);
        $peopleGestionnaire1->setHomePhoneNumber('0167123456');
        $peopleGestionnaire1->setCellPhoneNumber('0712345678');
        $peopleGestionnaire1->setWorkPhoneNumber('0112345678');
        $peopleGestionnaire1->setWorkFaxNumber('0134567890');
        $peopleGestionnaire1->setObservations('C\'est une bonne gestionnaire.');
        $peopleGestionnaire1->setSensitiveObservations('RAS');

        $userGestionnaire1->addResponsibility($roleGestion);
        $userGestionnaire1->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaire1->addResponsibility($roleAdherentE);
        $userGestionnaire1->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaire1);
        $peopleGestionnaire1->setUser($userGestionnaire1);
        $manager->persist($peopleGestionnaire1);

        //gest2
        $userGestionnaire2 = new User();
        $userGestionnaire2->setUsername('gest2');

        $password = $this->encoder->encodePassword($userGestionnaire2, 'a');
        $userGestionnaire2->setPassword($password);

        $peopleGestionnaire2 = new People();

        $peopleGestionnaire2->setDenomination($monsieur);
        $peopleGestionnaire2->setFirstName('Gesti');
        $peopleGestionnaire2->setLastName('Onnaire2');
        $peopleGestionnaire2->setEmailAddress('gestionnaire2@fake.mail');

        $peopleGestionnaire2Address = new Address('14 rue Victor Hugo', '34000', 'Montpellier', 'France');
        $manager->persist($peopleGestionnaire2Address);
        $peopleGestionnaire2->addAddress($peopleGestionnaire2Address);

        $peopleGestionnaire2->setIsReceivingNewsletter(true);
        $peopleGestionnaire2->setNewsletterDematerialization(false);
        $peopleGestionnaire2->setHomePhoneNumber('0167123456');
        $peopleGestionnaire2->setCellPhoneNumber('0712345678');
        $peopleGestionnaire2->setWorkPhoneNumber('0112345678');
        $peopleGestionnaire2->setWorkFaxNumber('0134567890');
        $peopleGestionnaire2->setObservations('C\'est un gestionnaire moyen mais sympathique.');
        $peopleGestionnaire2->setSensitiveObservations('RAS');

        $userGestionnaire2->addResponsibility($roleGestion);
        $userGestionnaire2->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaire2->addResponsibility($roleAdherentE);
        $userGestionnaire2->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaire2);
        $peopleGestionnaire2->setUser($userGestionnaire2);
        $manager->persist($peopleGestionnaire2);

        // Informateurice user
        $userInformateurice = new User();
        $userInformateurice->setUsername('info');

        $password = $this->encoder->encodePassword($userInformateurice, 'a');
        $userInformateurice->setPassword($password);

        $peopleInformateurice = new People();

        $peopleInformateurice->setDenomination($madame);
        $peopleInformateurice->setFirstName('Info');
        $peopleInformateurice->setLastName('Rmateurice');
        $peopleInformateurice->setEmailAddress('informateurice-sensible@fake.mail');

        $peopleInformateuriceAddress = new Address('137 Avenue Simone Veil', '69150', 'Décines-Charpieu', 'France');
        $manager->persist($peopleInformateuriceAddress);
        $peopleInformateurice->addAddress($peopleInformateuriceAddress);

        $peopleInformateurice->setIsReceivingNewsletter(false);
        $peopleInformateurice->setNewsletterDematerialization(false);
        $peopleInformateurice->setHomePhoneNumber('0167654321');
        $peopleInformateurice->setCellPhoneNumber('0787654321');
        $peopleInformateurice->setWorkPhoneNumber('0187654321');
        $peopleInformateurice->setWorkFaxNumber('0109876543');
        $peopleInformateurice->setObservations('Il serait bien de la faire adhérer.');
        $peopleInformateurice->setSensitiveObservations('RAS');

        $userInformateurice->addResponsibility($roleInformateurice);
        $userInformateurice->addResponsibility($roleInscritE);

        $manager->persist($userInformateurice);
        $peopleInformateurice->setUser($userInformateurice);
        $manager->persist($peopleInformateurice);

        // Adhérent.e user
        // adhe1
        $userAdherentE1 = new User();
        $userAdherentE1->setUsername('adhe1');

        $peopleAdherentE1 = new People();

        $password = $this->encoder->encodePassword($userAdherentE1, 'a');
        $userAdherentE1->setPassword($password);

        $peopleAdherentE1->setDenomination($madame);
        $peopleAdherentE1->setFirstName('Adhe1');
        $peopleAdherentE1->setLastName('Rente');
        $peopleAdherentE1->setEmailAddress('adherente1@fake.mail');

        $peopleAdherentE1Address = new Address('15 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE1Address);
        $peopleAdherentE1->addAddress($peopleAdherentE1Address);

        $peopleAdherentE1->setIsReceivingNewsletter(true);
        $peopleAdherentE1->setNewsletterDematerialization(false);
        $peopleAdherentE1->setHomePhoneNumber('0167123456');
        $peopleAdherentE1->setCellPhoneNumber('0712345678');
        $peopleAdherentE1->setWorkPhoneNumber('0112345678');
        $peopleAdherentE1->setWorkFaxNumber('0134567890');
        $peopleAdherentE1->setObservations('A appelé le 17/03/2019.');
        $peopleAdherentE1->setSensitiveObservations('Probablement à risque');

        $userAdherentE1->addResponsibility($roleAdherentE);
        $userAdherentE1->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE1);
        $peopleAdherentE1->setUser($userAdherentE1);
        $manager->persist($peopleAdherentE1);

        // adhe2
        $userAdherentE2 = new User();
        $userAdherentE2->setUsername('adhe2');

        $peopleAdherentE2 = new People();

        $password = $this->encoder->encodePassword($userAdherentE2, 'a');
        $userAdherentE2->setPassword($password);

        $peopleAdherentE2->setDenomination($madame);
        $peopleAdherentE2->setFirstName('Adhe2');
        $peopleAdherentE2->setLastName('Rente');
        $peopleAdherentE2->setEmailAddress('adherente2@fake.mail');

        $peopleAdherentE2Address = new Address('25 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE2Address);
        $peopleAdherentE2->addAddress($peopleAdherentE2Address);

        $peopleAdherentE2->setIsReceivingNewsletter(true);
        $peopleAdherentE2->setNewsletterDematerialization(true);
        $peopleAdherentE2->setHomePhoneNumber('0167123456');
        $peopleAdherentE2->setCellPhoneNumber('0712345678');
        $peopleAdherentE2->setWorkPhoneNumber('0112345678');
        $peopleAdherentE2->setWorkFaxNumber('0134567890');
        $peopleAdherentE2->setObservations('A appelé le 18/03/2019.');
        $peopleAdherentE2->setSensitiveObservations('Malade');

        $userAdherentE2->addResponsibility($roleAdherentE);
        $userAdherentE2->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE2);
        $peopleAdherentE2->setUser($userAdherentE2);
        $manager->persist($peopleAdherentE2);

        // adhe3
        $userAdherentE3 = new User();
        $userAdherentE3->setUsername('adhe3');

        $password = $this->encoder->encodePassword($userAdherentE3, 'a');
        $userAdherentE3->setPassword($password);

        $peopleAdherentE3 = new People();

        $peopleAdherentE3->setDenomination($madame);
        $peopleAdherentE3->setFirstName('Adhe3');
        $peopleAdherentE3->setLastName('Rente');
        $peopleAdherentE3->setEmailAddress('adherente3@fake.mail');

        $peopleAdherentE3Address = new Address('35 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE3Address);
        $peopleAdherentE3->addAddress($peopleAdherentE3Address);

        $peopleAdherentE3->setIsReceivingNewsletter(true);
        $peopleAdherentE3->setNewsletterDematerialization(false);
        $peopleAdherentE3->setHomePhoneNumber('0167123456');
        $peopleAdherentE3->setCellPhoneNumber('0712345678');
        $peopleAdherentE3->setWorkPhoneNumber('0112345678');
        $peopleAdherentE3->setWorkFaxNumber('0134567890');
        $peopleAdherentE3->setObservations('A appelé le 19/03/2019.');
        $peopleAdherentE3->setSensitiveObservations('Le père est atteint.');

        $userAdherentE3->addResponsibility($roleAdherentE);
        $userAdherentE3->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE3);
        $peopleAdherentE3->setUser($userAdherentE3);
        $manager->persist($peopleAdherentE3);

        // adhe4
        $userAdherentE4 = new User();
        $userAdherentE4->setUsername('adhe4');

        $password = $this->encoder->encodePassword($userAdherentE4, 'a');
        $userAdherentE4->setPassword($password);

        $peopleAdherentE4 = new People();

        $peopleAdherentE4->setDenomination($docteur);
        $peopleAdherentE4->setFirstName('Adhe4');
        $peopleAdherentE4->setLastName('Rente');
        $peopleAdherentE4->setEmailAddress('adherente4@fake.mail');

        $peopleAdherentE4Address = new Address('45 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE4Address);
        $peopleAdherentE4->addAddress($peopleAdherentE4Address);

        $peopleAdherentE4->setIsReceivingNewsletter(false);
        $peopleAdherentE4->setNewsletterDematerialization(false);
        $peopleAdherentE4->setHomePhoneNumber('0167123456');
        $peopleAdherentE4->setCellPhoneNumber('0712345678');
        $peopleAdherentE4->setWorkPhoneNumber('0112345678');
        $peopleAdherentE4->setWorkFaxNumber('0134567890');
        $peopleAdherentE4->setObservations('A appelé le 20/03/2019.');
        $peopleAdherentE4->setSensitiveObservations('Non touché par la maladie');

        $userAdherentE4->addResponsibility($roleAdherentE);
        $userAdherentE4->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE4);
        $peopleAdherentE4->setUser($userAdherentE4);
        $manager->persist($peopleAdherentE4);

        // adhe5
        $userAdherentE5 = new User();
        $userAdherentE5->setUsername('adhe5');

        $password = $this->encoder->encodePassword($userAdherentE5, 'a');
        $userAdherentE5->setPassword($password);

        $peopleAdherentE5 = new People();

        $peopleAdherentE5->setDenomination($docteure);
        $peopleAdherentE5->setFirstName('Adhe5');
        $peopleAdherentE5->setLastName('Rente');
        $peopleAdherentE5->setEmailAddress('adherente5@fake.mail');

        $peopleAdherentE5Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE5Address);
        $peopleAdherentE5->addAddress($peopleAdherentE5Address);

        $peopleAdherentE5->setIsReceivingNewsletter(true);
        $peopleAdherentE5->setNewsletterDematerialization(false);
        $peopleAdherentE5->setHomePhoneNumber('0167123456');
        $peopleAdherentE5->setCellPhoneNumber('0712345678');
        $peopleAdherentE5->setWorkPhoneNumber('0112345678');
        $peopleAdherentE5->setWorkFaxNumber('0134567890');
        $peopleAdherentE5->setObservations('A appelé le 21/03/2019.');
        $peopleAdherentE5->setSensitiveObservations('Résultat du test en attente');

        $userAdherentE5->addResponsibility($roleAdherentE);
        $userAdherentE5->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE5);
        $peopleAdherentE5->setUser($userAdherentE5);
        $manager->persist($peopleAdherentE5);

        // Final flush
        $manager->flush();
    }
}