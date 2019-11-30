<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\People;
use App\Entity\Responsibility;
use App\Entity\Address;
use App\Entity\Denomination;
use App\Repository\ResponsibilityRepository;


class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }

    public function load(ObjectManager $manager)
    {
        /***************************************************/
        /*  Responsibilities are created during migration  */
        /***************************************************/
        $responsibilityRepository = $manager->getRepository(Responsibility::class);
        $roleAdmin = $responsibilityRepository->findOneBy(['code' => 'ROLE_ADMIN']);
        $roleAdminSensible = $responsibilityRepository->findOneBy(['code' => 'ROLE_ADMIN_SENSIBLE']);
        $roleGestion = $responsibilityRepository->findOneBy(['code' => 'ROLE_GESTION']);
        $roleGestionSensible = $responsibilityRepository->findOneBy(['code' => 'ROLE_GESTION_SENSIBLE']);
        $roleInformateurice = $responsibilityRepository->findOneBy(['code' => 'ROLE_INFORMATEURICE']);
        $roleAdherentE = $responsibilityRepository->findOneBy(['code' => 'ROLE_ADHERENT_E']);
        $roleExAdherentE = $responsibilityRepository->findOneBy(['code' => 'ROLE_EX_ADHERENT_E']);
        $roleMecene = $responsibilityRepository->findOneBy(['code' => 'ROLE_MECENE']);
        $roleSympathisantE = $responsibilityRepository->findOneBy(['code' => 'ROLE_SYMPATHISANT_E']);
        $roleConsultationAnnuaire = $responsibilityRepository->findOneBy(['code' => 'ROLE_CONSULTATION_ANNUAIRE']);
        $roleInscritE = $responsibilityRepository->findOneBy(['code' => 'ROLE_INSCRIT_E']);

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

        $passwordAdminSensible = $this->encoder->encodePassword($userAdminSensible, 'a');
        $userAdminSensible->setPassword($passwordAdminSensible);

        $peopleAdminSensible = new People();

        $peopleAdminSensible->setDenomination($monsieur);
        $peopleAdminSensible->setFirstName('Hubert');
        $peopleAdminSensible->setLastName('Schaeffer');
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

        $passwordAdmin = $this->encoder->encodePassword($userAdmin, 'a');
        $userAdmin->setPassword($passwordAdmin);

        $peopleAdmin = new People();

        $peopleAdmin->setDenomination($mix);
        $peopleAdmin->setFirstName('Joël');
        $peopleAdmin->setLastName('Halphen');
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

        $passwordGestionnaireSensible = $this->encoder->encodePassword($userGestionnaireSensible, 'a');
        $userGestionnaireSensible->setPassword($passwordGestionnaireSensible);

        $peopleGestionnaireSensible = new People();

        $peopleGestionnaireSensible->setDenomination($mix);
        $peopleGestionnaireSensible->setFirstName('Tobie');
        $peopleGestionnaireSensible->setLastName('Soyer');
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
        // gest1
        $userGestionnaire1 = new User();
        $userGestionnaire1->setUsername('gest1');

        $passwordGestionnaire1 = $this->encoder->encodePassword($userGestionnaire1, 'a');
        $userGestionnaire1->setPassword($passwordGestionnaire1);

        $peopleGestionnaire1 = new People();

        $peopleGestionnaire1->setDenomination($monsieur);
        $peopleGestionnaire1->setFirstName('Hugo');
        $peopleGestionnaire1->setLastName('Trintignant');
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

        // gest2
        $userGestionnaire2 = new User();
        $userGestionnaire2->setUsername('gest2');

        $passwordGestionnaire2 = $this->encoder->encodePassword($userGestionnaire2, 'a');
        $userGestionnaire2->setPassword($passwordGestionnaire2);

        $peopleGestionnaire2 = new People();

        $peopleGestionnaire2->setDenomination($mix);
        $peopleGestionnaire2->setFirstName('Alceste');
        $peopleGestionnaire2->setLastName('De Verley');
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

        $passwordInformateurice = $this->encoder->encodePassword($userInformateurice, 'a');
        $userInformateurice->setPassword($passwordInformateurice);

        $peopleInformateurice = new People();

        $peopleInformateurice->setDenomination($madame);
        $peopleInformateurice->setFirstName('Catherine');
        $peopleInformateurice->setLastName('Dubois');
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

        $passwordAdherentE1 = $this->encoder->encodePassword($userAdherentE1, 'a');
        $userAdherentE1->setPassword($passwordAdherentE1);

        $peopleAdherentE1 = new People();

        $peopleAdherentE1->setDenomination($madame);
        $peopleAdherentE1->setFirstName('Jeanne');
        $peopleAdherentE1->setLastName('Vérany');
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

        $passwordAdherentE2 = $this->encoder->encodePassword($userAdherentE2, 'a');
        $userAdherentE2->setPassword($passwordAdherentE2);

        $peopleAdherentE2 = new People();

        $peopleAdherentE2->setDenomination($madame);
        $peopleAdherentE2->setFirstName('Arlette');
        $peopleAdherentE2->setLastName('Maurice');
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

        $passwordAdherentE3 = $this->encoder->encodePassword($userAdherentE3, 'a');
        $userAdherentE3->setPassword($passwordAdherentE3);

        $peopleAdherentE3 = new People();

        $peopleAdherentE3->setDenomination($docteur);
        $peopleAdherentE3->setFirstName('Ladislas');
        $peopleAdherentE3->setLastName('Bullion');
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

        $passwordAdherentE4 = $this->encoder->encodePassword($userAdherentE4, 'a');
        $userAdherentE4->setPassword($passwordAdherentE4);

        $peopleAdherentE4 = new People();

        $peopleAdherentE4->setDenomination($docteure);
        $peopleAdherentE4->setFirstName('Estelle');
        $peopleAdherentE4->setLastName('Lafaille');
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

        $passwordAdherentE5 = $this->encoder->encodePassword($userAdherentE5, 'a');
        $userAdherentE5->setPassword($passwordAdherentE5);

        $peopleAdherentE5 = new People();

        $peopleAdherentE5->setDenomination($docteure);
        $peopleAdherentE5->setFirstName('Agathe');
        $peopleAdherentE5->setLastName('Duval');
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

        // adhe6
        $userAdherentE6 = new User();
        $userAdherentE6->setUsername('adhe6');

        $passwordAdherentE6 = $this->encoder->encodePassword($userAdherentE6, 'a');
        $userAdherentE6->setPassword($passwordAdherentE6);

        $peopleAdherentE6 = new People();

        $peopleAdherentE6->setDenomination($madame);
        $peopleAdherentE6->setFirstName('Christine');
        $peopleAdherentE6->setLastName('Rit');
        $peopleAdherentE6->setEmailAddress('adherente6@fake.mail');

        $peopleAdherentE6Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE6Address);
        $peopleAdherentE6->addAddress($peopleAdherentE6Address);

        $peopleAdherentE6->setIsReceivingNewsletter(true);
        $peopleAdherentE6->setNewsletterDematerialization(false);
        $peopleAdherentE6->setHomePhoneNumber('0167123456');
        $peopleAdherentE6->setCellPhoneNumber('0712345678');
        $peopleAdherentE6->setWorkPhoneNumber('0112345678');

        $userAdherentE6->addResponsibility($roleAdherentE);
        $userAdherentE6->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE6);
        $peopleAdherentE6->setUser($userAdherentE6);
        $manager->persist($peopleAdherentE6);

        // adhe7
        $userAdherentE7 = new User();
        $userAdherentE7->setUsername('adhe7');

        $passwordAdherentE7 = $this->encoder->encodePassword($userAdherentE7, 'a');
        $userAdherentE7->setPassword($passwordAdherentE7);

        $peopleAdherentE7 = new People();

        $peopleAdherentE7->setDenomination($madame);
        $peopleAdherentE7->setFirstName('Maurelle');
        $peopleAdherentE7->setLastName('Lespérance');
        $peopleAdherentE7->setEmailAddress('adherente7@fake.mail');

        $peopleAdherentE7Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE7Address);
        $peopleAdherentE7->addAddress($peopleAdherentE7Address);

        $peopleAdherentE7->setIsReceivingNewsletter(true);
        $peopleAdherentE7->setNewsletterDematerialization(false);
        $peopleAdherentE7->setHomePhoneNumber('0167123456');
        $peopleAdherentE7->setCellPhoneNumber('0712345678');
        $peopleAdherentE7->setWorkPhoneNumber('0112345678');

        $userAdherentE7->addResponsibility($roleAdherentE);
        $userAdherentE7->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE7);
        $peopleAdherentE7->setUser($userAdherentE7);
        $manager->persist($peopleAdherentE7);

        // adhe8
        $userAdherentE8 = new User();
        $userAdherentE8->setUsername('adhe8');

        $passwordAdherentE8 = $this->encoder->encodePassword($userAdherentE8, 'a');
        $userAdherentE8->setPassword($passwordAdherentE8);

        $peopleAdherentE8 = new People();

        $peopleAdherentE8->setDenomination($madame);
        $peopleAdherentE8->setFirstName('Joseph');
        $peopleAdherentE8->setLastName('Lagueux');
        $peopleAdherentE8->setEmailAddress('adherente8@fake.mail');

        $peopleAdherentE8Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE8Address);
        $peopleAdherentE8->addAddress($peopleAdherentE8Address);

        $peopleAdherentE8->setIsReceivingNewsletter(true);
        $peopleAdherentE8->setNewsletterDematerialization(false);
        $peopleAdherentE8->setHomePhoneNumber('0167123456');
        $peopleAdherentE8->setCellPhoneNumber('0712345678');
        $peopleAdherentE8->setWorkPhoneNumber('0112345678');

        $userAdherentE8->addResponsibility($roleAdherentE);
        $userAdherentE8->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE8);
        $peopleAdherentE8->setUser($userAdherentE8);
        $manager->persist($peopleAdherentE8);

        // adhe9
        $userAdherentE9 = new User();
        $userAdherentE9->setUsername('adhe9');

        $passwordAdherentE9 = $this->encoder->encodePassword($userAdherentE9, 'a');
        $userAdherentE9->setPassword($passwordAdherentE9);

        $peopleAdherentE9 = new People();

        $peopleAdherentE9->setDenomination($madame);
        $peopleAdherentE9->setFirstName('Mirabelle');
        $peopleAdherentE9->setLastName('Totah');
        $peopleAdherentE9->setEmailAddress('adherente9@fake.mail');

        $peopleAdherentE9Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE9Address);
        $peopleAdherentE9->addAddress($peopleAdherentE9Address);

        $peopleAdherentE9->setIsReceivingNewsletter(true);
        $peopleAdherentE9->setNewsletterDematerialization(false);
        $peopleAdherentE9->setHomePhoneNumber('0167123456');
        $peopleAdherentE9->setCellPhoneNumber('0712345678');
        $peopleAdherentE9->setWorkPhoneNumber('0112345678');

        $userAdherentE9->addResponsibility($roleAdherentE);
        $userAdherentE9->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE9);
        $peopleAdherentE9->setUser($userAdherentE9);
        $manager->persist($peopleAdherentE9);

        // adhe10
        $userAdherentE10 = new User();
        $userAdherentE10->setUsername('adhe10');

        $passwordAdherentE10 = $this->encoder->encodePassword($userAdherentE10, 'a');
        $userAdherentE10->setPassword($passwordAdherentE10);

        $peopleAdherentE10 = new People();

        $peopleAdherentE10->setDenomination($madame);
        $peopleAdherentE10->setFirstName('Myriam');
        $peopleAdherentE10->setLastName('Kouri');
        $peopleAdherentE10->setEmailAddress('adherente10@fake.mail');

        $peopleAdherentE10Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE10Address);
        $peopleAdherentE10->addAddress($peopleAdherentE10Address);

        $peopleAdherentE10->setIsReceivingNewsletter(true);
        $peopleAdherentE10->setNewsletterDematerialization(false);
        $peopleAdherentE10->setHomePhoneNumber('0167123456');
        $peopleAdherentE10->setCellPhoneNumber('0712345678');
        $peopleAdherentE10->setWorkPhoneNumber('0112345678');

        $userAdherentE10->addResponsibility($roleAdherentE);
        $userAdherentE10->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE10);
        $peopleAdherentE10->setUser($userAdherentE10);
        $manager->persist($peopleAdherentE10);

        // adhe11
        $userAdherentE11 = new User();
        $userAdherentE11->setUsername('adhe11');

        $passwordAdherentE11 = $this->encoder->encodePassword($userAdherentE11, 'a');
        $userAdherentE11->setPassword($passwordAdherentE11);

        $peopleAdherentE11 = new People();

        $peopleAdherentE11->setDenomination($madame);
        $peopleAdherentE11->setFirstName('Wei');
        $peopleAdherentE11->setLastName('Ch\'eng');
        $peopleAdherentE11->setEmailAddress('adherente11@fake.mail');

        $peopleAdherentE11Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE11Address);
        $peopleAdherentE11->addAddress($peopleAdherentE11Address);

        $peopleAdherentE11->setIsReceivingNewsletter(true);
        $peopleAdherentE11->setNewsletterDematerialization(false);
        $peopleAdherentE11->setHomePhoneNumber('0167123456');
        $peopleAdherentE11->setCellPhoneNumber('0712345678');
        $peopleAdherentE11->setWorkPhoneNumber('0112345678');

        $userAdherentE11->addResponsibility($roleAdherentE);
        $userAdherentE11->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE11);
        $peopleAdherentE11->setUser($userAdherentE11);
        $manager->persist($peopleAdherentE11);

        // adhe12
        $userAdherentE12 = new User();
        $userAdherentE12->setUsername('adhe12');

        $passwordAdherentE12 = $this->encoder->encodePassword($userAdherentE12, 'a');
        $userAdherentE12->setPassword($passwordAdherentE12);

        $peopleAdherentE12 = new People();

        $peopleAdherentE12->setDenomination($mix);
        $peopleAdherentE12->setFirstName('Jaska');
        $peopleAdherentE12->setLastName('Reho');
        $peopleAdherentE12->setEmailAddress('adherente12@fake.mail');

        $peopleAdherentE12Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE12Address);
        $peopleAdherentE12->addAddress($peopleAdherentE12Address);

        $peopleAdherentE12->setIsReceivingNewsletter(true);
        $peopleAdherentE12->setNewsletterDematerialization(false);
        $peopleAdherentE12->setHomePhoneNumber('0167123456');
        $peopleAdherentE12->setCellPhoneNumber('0712345678');
        $peopleAdherentE12->setWorkPhoneNumber('0112345678');

        $userAdherentE12->addResponsibility($roleAdherentE);
        $userAdherentE12->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE12);
        $peopleAdherentE12->setUser($userAdherentE12);
        $manager->persist($peopleAdherentE12);

        // adhe13
        $userAdherentE13 = new User();
        $userAdherentE13->setUsername('adhe13');

        $passwordAdherentE13 = $this->encoder->encodePassword($userAdherentE13, 'a');
        $userAdherentE13->setPassword($passwordAdherentE13);

        $peopleAdherentE13 = new People();

        $peopleAdherentE13->setDenomination($madame);
        $peopleAdherentE13->setFirstName('Arbi');
        $peopleAdherentE13->setLastName('Musliyevich');
        $peopleAdherentE13->setEmailAddress('adherente13@fake.mail');

        $peopleAdherentE13Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE13Address);
        $peopleAdherentE13->addAddress($peopleAdherentE13Address);

        $peopleAdherentE13->setIsReceivingNewsletter(true);
        $peopleAdherentE13->setNewsletterDematerialization(false);
        $peopleAdherentE13->setHomePhoneNumber('0167123456');
        $peopleAdherentE13->setCellPhoneNumber('0712345678');
        $peopleAdherentE13->setWorkPhoneNumber('0112345678');

        $userAdherentE13->addResponsibility($roleAdherentE);
        $userAdherentE13->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE13);
        $peopleAdherentE13->setUser($userAdherentE13);
        $manager->persist($peopleAdherentE13);

        // adhe14
        $userAdherentE14 = new User();
        $userAdherentE14->setUsername('adhe14');

        $passwordAdherentE14 = $this->encoder->encodePassword($userAdherentE14, 'a');
        $userAdherentE14->setPassword($passwordAdherentE14);

        $peopleAdherentE14 = new People();

        $peopleAdherentE14->setDenomination($madame);
        $peopleAdherentE14->setFirstName('Hana');
        $peopleAdherentE14->setLastName('Moravcová');
        $peopleAdherentE14->setEmailAddress('adherente14@fake.mail');

        $peopleAdherentE14Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE14Address);
        $peopleAdherentE14->addAddress($peopleAdherentE14Address);

        $peopleAdherentE14->setIsReceivingNewsletter(true);
        $peopleAdherentE14->setNewsletterDematerialization(false);
        $peopleAdherentE14->setHomePhoneNumber('0167123456');
        $peopleAdherentE14->setCellPhoneNumber('0712345678');
        $peopleAdherentE14->setWorkPhoneNumber('0112345678');

        $userAdherentE14->addResponsibility($roleAdherentE);
        $userAdherentE14->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE14);
        $peopleAdherentE14->setUser($userAdherentE14);
        $manager->persist($peopleAdherentE14);

        // adhe15
        $userAdherentE15 = new User();
        $userAdherentE15->setUsername('adhe15');

        $passwordAdherentE15 = $this->encoder->encodePassword($userAdherentE15, 'a');
        $userAdherentE15->setPassword($passwordAdherentE15);

        $peopleAdherentE15 = new People();

        $peopleAdherentE15->setDenomination($madame);
        $peopleAdherentE15->setFirstName('Renata');
        $peopleAdherentE15->setLastName('Milić');
        $peopleAdherentE15->setEmailAddress('adherente15@fake.mail');

        $peopleAdherentE15Address = new Address('55 Rue Emile Zola', '69100', 'Villeurbanne', 'France');
        $manager->persist($peopleAdherentE15Address);
        $peopleAdherentE15->addAddress($peopleAdherentE15Address);

        $peopleAdherentE15->setIsReceivingNewsletter(true);
        $peopleAdherentE15->setNewsletterDematerialization(false);
        $peopleAdherentE15->setHomePhoneNumber('0167123456');
        $peopleAdherentE15->setCellPhoneNumber('0712345678');
        $peopleAdherentE15->setWorkPhoneNumber('0112345678');

        $userAdherentE15->addResponsibility($roleAdherentE);
        $userAdherentE15->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE15);
        $peopleAdherentE15->setUser($userAdherentE15);
        $manager->persist($peopleAdherentE15);

        // supportive user
        $userSupportive = new User();
        $userSupportive->setUsername('supp1');

        $passwordSupportive = $this->encoder->encodePassword($userSupportive, 'a');
        $userSupportive->setPassword($passwordSupportive);

        $peopleSupportive = new People();

        $peopleSupportive->setDenomination($docteure);
        $peopleSupportive->setFirstName('Morgane');
        $peopleSupportive->setLastName('Cauw');
        $peopleSupportive->setEmailAddress('supportive@fake.mail');

        $peopleSupportiveAddress = new Address('127 Rue Ada Lovelace', '34000', 'Montpellier', 'France');
        $manager->persist($peopleSupportiveAddress);
        $peopleSupportive->addAddress($peopleSupportiveAddress);

        $peopleSupportive->setIsReceivingNewsletter(true);
        $peopleSupportive->setNewsletterDematerialization(false);
        $peopleSupportive->setHomePhoneNumber('0167123456');
        $peopleSupportive->setCellPhoneNumber('0712345678');
        $peopleSupportive->setWorkPhoneNumber('0112345678');
        $peopleSupportive->setWorkFaxNumber('0134567890');
        $peopleSupportive->setObservations('A demandé des informations lors du gala de 2018.');
        $peopleSupportive->setSensitiveObservations('');

        $userSupportive->addResponsibility($roleSympathisantE);
        $userSupportive->addResponsibility($roleInscritE);

        $manager->persist($userSupportive);
        $peopleSupportive->setUser($userSupportive);
        $manager->persist($peopleSupportive);

        // rich user
        $userRich = new User();
        $userRich->setUsername('rich1');

        $passwordRich = $this->encoder->encodePassword($userRich, 'a');
        $userRich->setPassword($passwordRich);

        $peopleRich = new People();

        $peopleRich->setDenomination($docteure);
        $peopleRich->setFirstName('Elliot');
        $peopleRich->setLastName('Kronem');
        $peopleRich->setEmailAddress('rich@fake.mail');

        $peopleRichAddress = new Address('12 Avenue des Champs-Élysées', '75008', 'Paris', 'France');
        $manager->persist($peopleRichAddress);
        $peopleRich->addAddress($peopleRichAddress);

        $peopleRich->setIsReceivingNewsletter(true);
        $peopleRich->setNewsletterDematerialization(false);
        $peopleRich->setHomePhoneNumber('0167123456');
        $peopleRich->setCellPhoneNumber('0712345678');
        $peopleRich->setWorkPhoneNumber('0112345678');
        $peopleRich->setWorkFaxNumber('0134567890');
        $peopleRich->setObservations('A donné un chèque de 8000€ au gala de 2018.');
        $peopleRich->setSensitiveObservations('');

        $userRich->addResponsibility($roleMecene);
        $userRich->addResponsibility($roleInscritE);

        $manager->persist($userRich);
        $peopleRich->setUser($userRich);
        $manager->persist($peopleRich);

        // Admin uniquement
        $userAdminUniquement = new User();
        $userAdminUniquement ->setUsername('adminUniquement');

        $passwordAdminUniquement = $this->encoder->encodePassword($userAdminUniquement, 'a');
        $userAdminUniquement->setPassword($passwordAdminUniquement);

        $peopleAdminUniquement = new People();

        $peopleAdminUniquement->setDenomination($madame);
        $peopleAdminUniquement->setFirstName('Mélissa');
        $peopleAdminUniquement->setLastName('Truc');
        $peopleAdminUniquement->setEmailAddress('administrator-life@fake.mail');

        $peopleAdminUniquementAddress = new Address('10 rue des catacombes', '13001', 'Marseille', 'France');
        $manager->persist($peopleAdminUniquementAddress);
        $peopleAdminUniquement->addAddress($peopleAdminUniquementAddress);

        $peopleAdminUniquement->setIsReceivingNewsletter(true);
        $peopleAdminUniquement->setNewsletterDematerialization(true);
        $peopleAdminUniquement->setHomePhoneNumber('0467654321');
        $peopleAdminUniquement->setCellPhoneNumber('0687654321');
        $peopleAdminUniquement->setWorkPhoneNumber('0487654321');
        $peopleAdminUniquement->setWorkFaxNumber('0409876543');
        $peopleAdminUniquement->setObservations(':3c');
        $peopleAdminUniquement->setSensitiveObservations('RAS');

        $userAdminUniquement->addResponsibility($roleAdmin);
        $userAdminUniquement->addResponsibility($roleInscritE);

        $manager->persist($userAdminUniquement);
        $peopleAdminUniquement->setUser($userAdminUniquement);
        $manager->persist($peopleAdminUniquement);

        // Inscrite uniquement
        $userRegistered = new User();
        $userRegistered ->setUsername('inscr');

        $passwordRegistered = $this->encoder->encodePassword($userRegistered, 'a');
        $userRegistered->setPassword($passwordRegistered);

        $peopleRegistered = new People();

        $peopleRegistered->setDenomination($madame);
        $peopleRegistered->setFirstName('Jodi');
        $peopleRegistered->setLastName('Manchon');
        $peopleRegistered->setEmailAddress('inscrite@fake.mail');

        $peopleRegisteredAddress = new Address('22 rue des écureuils', '13001', 'Marseille', 'France');
        $manager->persist($peopleRegisteredAddress);
        $peopleRegistered->addAddress($peopleRegisteredAddress);

        $peopleRegistered->setIsReceivingNewsletter(true);
        $peopleRegistered->setNewsletterDematerialization(true);
        $peopleRegistered->setHomePhoneNumber('0467654321');
        $peopleRegistered->setCellPhoneNumber('0687654321');
        $peopleRegistered->setWorkPhoneNumber('0487654321');
        $peopleRegistered->setWorkFaxNumber('0409876543');
        $peopleRegistered->setObservations('Notée suite à appel téléphonique du 23 juillet 2019.');
        $peopleRegistered->setSensitiveObservations('');

        $userRegistered->addResponsibility($roleInscritE);

        $manager->persist($userRegistered);
        $peopleRegistered->setUser($userRegistered);
        $manager->persist($peopleRegistered);

        // test
        $userTest = new User();
        $userTest ->setUsername('test');

        $passwordTest = $this->encoder->encodePassword($userTest, 'a');
        $userTest->setPassword($passwordTest);

        $peopleTest = new People();

        $peopleTest->setDenomination($madame);
        $peopleTest->setFirstName('Mélissa');
        $peopleTest->setLastName('Dubois');
        $peopleTest->setEmailAddress('administrator-life@fake.mail');

        $peopleTestAddress = new Address('10 rue des catacombes', '13001', 'Marseille', 'France');
        $manager->persist($peopleTestAddress);
        $peopleTest->addAddress($peopleTestAddress);

        $peopleTest->setIsReceivingNewsletter(true);
        $peopleTest->setNewsletterDematerialization(true);
        $peopleTest->setHomePhoneNumber('0467654321');
        $peopleTest->setCellPhoneNumber('0687654321');
        $peopleTest->setWorkPhoneNumber('0487654321');
        $peopleTest->setWorkFaxNumber('0409876543');
        $peopleTest->setObservations(':3c');
        $peopleTest->setSensitiveObservations('RAS');

        $userTest->addResponsibility($roleAdmin);
        $userTest->addResponsibility($roleInscritE);

        $manager->persist($userTest);
        $peopleTest->setUser($userTest);
        $manager->persist($peopleTest);

        // test2
        $userTest2 = new User();
        $userTest2 ->setUsername('test2');

        $password = $this->encoder->encodePassword($userTest2, 'a');
        $userTest2->setPassword($password);

        $peopleTest2 = new People();

        $peopleTest2->setDenomination($madame);
        $peopleTest2->setFirstName('Jeanne');
        $peopleTest2->setLastName('Carton');
        $peopleTest2->setEmailAddress('administrator-life@fake.mail');

        $peopleTest2Address = new Address('10 rue des catacombes', '13001', 'Marseille', 'France');
        $manager->persist($peopleTest2Address);
        $peopleTest2->addAddress($peopleTest2Address);

        $peopleTest2->setIsReceivingNewsletter(true);
        $peopleTest2->setNewsletterDematerialization(true);
        $peopleTest2->setHomePhoneNumber('0467654321');
        $peopleTest2->setCellPhoneNumber('0687654321');
        $peopleTest2->setWorkPhoneNumber('0487654321');
        $peopleTest2->setWorkFaxNumber('0409876543');
        $peopleTest2->setObservations(':3c');
        $peopleTest2->setSensitiveObservations('RAS');

        $userTest2->addResponsibility($roleAdmin);
        $userTest2->addResponsibility($roleInscritE);

        $manager->persist($userTest2);
        $peopleTest2->setUser($userTest2);
        $manager->persist($peopleTest2);

        // Final flush
        $manager->flush();
    }
}
