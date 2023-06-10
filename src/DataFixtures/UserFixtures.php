<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Entity\User;
use App\Entity\People;
use App\Entity\Responsibility;
use App\Entity\Address;
use App\Entity\Denomination;
use App\Repository\ResponsibilityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }

    public function load(ObjectManager $manager)
    {
        // Create logger used to display information messages
        $output = new ConsoleOutput();

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
        $output->writeln('      <comment>></comment> <info>Denominations creation...</info>');

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
        $output->writeln('      <comment>></comment> <info>Users creation...</info>');
        $output->writeln('         <comment>></comment> <info>Admins creation...</info>');

        // Admin sensible user
        $userAdminSensible = new User();
        $userAdminSensible->setUsername('adminSensible');

        $passwordAdminSensible = $this->hasher->hashPassword($userAdminSensible, 'a');
        $userAdminSensible->setPassword($passwordAdminSensible);

        $peopleAdminSensible = new People();

        $peopleAdminSensible->setDenomination($monsieur);
        $peopleAdminSensible->setFirstName('Hubert');
        $peopleAdminSensible->setLastName('Schaeffer');
        $peopleAdminSensible->setEmailAddress('administrator-sensible@fake.mail');

        $peopleAdminSensibleAddress = new Address('101 rue Nancy Cárdenas', null, '59122', 'Cambrai', 'France');
        $manager->persist($peopleAdminSensibleAddress);
        $peopleAdminSensible->addAddress($peopleAdminSensibleAddress);

        $peopleAdminSensible->setIsReceivingNewsletter(true);
        $peopleAdminSensible->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdminSensible->setHomePhoneNumber($homePhoneNumber);
        $peopleAdminSensible->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdminSensible->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdminSensible->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordAdmin = $this->hasher->hashPassword($userAdmin, 'a');
        $userAdmin->setPassword($passwordAdmin);

        $peopleAdmin = new People();

        $peopleAdmin->setDenomination($mix);
        $peopleAdmin->setFirstName('Joël');
        $peopleAdmin->setLastName('Halphen');
        $peopleAdmin->setEmailAddress('administrator@fake.mail');

        $peopleAdminAddress = new Address('2 rue de la mine', null, '34000', 'Montpellier', 'France');
        $manager->persist($peopleAdminAddress);
        $peopleAdmin->addAddress($peopleAdminAddress);

        $peopleAdmin->setIsReceivingNewsletter(true);
        $peopleAdmin->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdmin->setHomePhoneNumber($homePhoneNumber);
        $peopleAdmin->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdmin->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdmin->setWorkFaxNumber($workFaxPhoneNumber);
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

        // Admin uniquement
        $userAdminUniquement = new User();
        $userAdminUniquement ->setUsername('adminUniquement');

        $passwordAdminUniquement = $this->hasher->hashPassword($userAdminUniquement, 'a');
        $userAdminUniquement->setPassword($passwordAdminUniquement);

        $peopleAdminUniquement = new People();

        $peopleAdminUniquement->setDenomination($madame);
        $peopleAdminUniquement->setFirstName('Mélissa');
        $peopleAdminUniquement->setLastName('Truc');
        $peopleAdminUniquement->setEmailAddress('administrator-life@fake.mail');

        $peopleAdminUniquementAddress = new Address('102 rue Simon Nkoli', 'Chez Corinne Khali', '83129', 'Six-Fours-les-Plages', 'France');
        $manager->persist($peopleAdminUniquementAddress);
        $peopleAdminUniquement->addAddress($peopleAdminUniquementAddress);

        $peopleAdminUniquement->setIsReceivingNewsletter(true);
        $peopleAdminUniquement->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdminUniquement->setHomePhoneNumber($homePhoneNumber);
        $peopleAdminUniquement->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdminUniquement->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdminUniquement->setWorkFaxNumber($workFaxPhoneNumber);
        $peopleAdminUniquement->setObservations(':3c');
        $peopleAdminUniquement->setSensitiveObservations('RAS');

        $userAdminUniquement->addResponsibility($roleAdmin);
        $userAdminUniquement->addResponsibility($roleInscritE);

        $manager->persist($userAdminUniquement);
        $peopleAdminUniquement->setUser($userAdminUniquement);
        $manager->persist($peopleAdminUniquement);

        $output->writeln('         <comment>></comment> <info>Manager creation...</info>');
        // Gestionnaire sensible user
        $userGestionnaireSensible = new User();
        $userGestionnaireSensible->setUsername('gestiSensible');

        $passwordGestionnaireSensible = $this->hasher->hashPassword($userGestionnaireSensible, 'a');
        $userGestionnaireSensible->setPassword($passwordGestionnaireSensible);

        $peopleGestionnaireSensible = new People();

        $peopleGestionnaireSensible->setDenomination($mix);
        $peopleGestionnaireSensible->setFirstName('Tobie');
        $peopleGestionnaireSensible->setLastName('Soyer');
        $peopleGestionnaireSensible->setEmailAddress('gestionnaire-sensible@fake.mail');

        $peopleGestionnaireSensibleAddress = new Address('563 rue Olympe de Gouges', null, '34730', 'Prades-le-Lez', 'France');
        $manager->persist($peopleGestionnaireSensibleAddress);
        $peopleGestionnaireSensible->addAddress($peopleGestionnaireSensibleAddress);

        $peopleGestionnaireSensible->setIsReceivingNewsletter(true);
        $peopleGestionnaireSensible->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleGestionnaireSensible->setHomePhoneNumber($homePhoneNumber);
        $peopleGestionnaireSensible->setCellPhoneNumber($cellPhoneNumber);
        $peopleGestionnaireSensible->setWorkPhoneNumber($workPhoneNumber);
        $peopleGestionnaireSensible->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordGestionnaire1 = $this->hasher->hashPassword($userGestionnaire1, 'a');
        $userGestionnaire1->setPassword($passwordGestionnaire1);

        $peopleGestionnaire1 = new People();

        $peopleGestionnaire1->setDenomination($monsieur);
        $peopleGestionnaire1->setFirstName('Hugo');
        $peopleGestionnaire1->setLastName('Trintignant');
        $peopleGestionnaire1->setEmailAddress('gestionnaire1@fake.mail');

        $peopleGestionnaire1Address = new Address('4 rue Denise Oliver-Melez', null, '13104', 'Arles', 'France');
        $manager->persist($peopleGestionnaire1Address);
        $peopleGestionnaire1->addAddress($peopleGestionnaire1Address);

        $peopleGestionnaire1->setIsReceivingNewsletter(true);
        $peopleGestionnaire1->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleGestionnaire1->setHomePhoneNumber($homePhoneNumber);
        $peopleGestionnaire1->setCellPhoneNumber($cellPhoneNumber);
        $peopleGestionnaire1->setWorkPhoneNumber($workPhoneNumber);
        $peopleGestionnaire1->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordGestionnaire2 = $this->hasher->hashPassword($userGestionnaire2, 'a');
        $userGestionnaire2->setPassword($passwordGestionnaire2);

        $peopleGestionnaire2 = new People();

        $peopleGestionnaire2->setDenomination($mix);
        $peopleGestionnaire2->setFirstName('Alceste');
        $peopleGestionnaire2->setLastName('De Verley');
        $peopleGestionnaire2->setEmailAddress('gestionnaire2@fake.mail');

        $peopleGestionnaire2Address = new Address('14 rue Iris Morales', null, '93300', 'Aubervilliers', 'France');
        $manager->persist($peopleGestionnaire2Address);
        $peopleGestionnaire2->addAddress($peopleGestionnaire2Address);

        $peopleGestionnaire2->setIsReceivingNewsletter(true);
        $peopleGestionnaire2->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleGestionnaire2->setHomePhoneNumber($homePhoneNumber);
        $peopleGestionnaire2->setCellPhoneNumber($cellPhoneNumber);
        $peopleGestionnaire2->setWorkPhoneNumber($workPhoneNumber);
        $peopleGestionnaire2->setWorkFaxNumber($workFaxPhoneNumber);
        $peopleGestionnaire2->setObservations('C\'est un gestionnaire moyen mais sympathique.');
        $peopleGestionnaire2->setSensitiveObservations('RAS');

        $userGestionnaire2->addResponsibility($roleGestion);
        $userGestionnaire2->addResponsibility($roleConsultationAnnuaire);
        $userGestionnaire2->addResponsibility($roleAdherentE);
        $userGestionnaire2->addResponsibility($roleInscritE);

        $manager->persist($userGestionnaire2);
        $peopleGestionnaire2->setUser($userGestionnaire2);
        $manager->persist($peopleGestionnaire2);

        $output->writeln('         <comment>></comment> <info>Community managers creation...</info>');
        // Informateurice user
        $userInformateurice = new User();
        $userInformateurice->setUsername('info');

        $passwordInformateurice = $this->hasher->hashPassword($userInformateurice, 'a');
        $userInformateurice->setPassword($passwordInformateurice);

        $peopleInformateurice = new People();

        $peopleInformateurice->setDenomination($madame);
        $peopleInformateurice->setFirstName('Catherine');
        $peopleInformateurice->setLastName('Dubois');
        $peopleInformateurice->setEmailAddress('informateurice-sensible@fake.mail');

        $peopleInformateuriceAddress = new Address('137 Avenue Simone Veil', 'Bâtiment 45', '69150', 'Décines-Charpieu', 'France');
        $manager->persist($peopleInformateuriceAddress);
        $peopleInformateurice->addAddress($peopleInformateuriceAddress);

        $peopleInformateurice->setIsReceivingNewsletter(false);
        $peopleInformateurice->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleInformateurice->setHomePhoneNumber($homePhoneNumber);
        $peopleInformateurice->setCellPhoneNumber($cellPhoneNumber);
        $peopleInformateurice->setWorkPhoneNumber($workPhoneNumber);
        $peopleInformateurice->setWorkFaxNumber($workFaxPhoneNumber);
        $peopleInformateurice->setObservations('Il serait bien de la faire adhérer.');
        $peopleInformateurice->setSensitiveObservations('RAS');

        $userInformateurice->addResponsibility($roleInformateurice);
        $userInformateurice->addResponsibility($roleInscritE);

        $manager->persist($userInformateurice);
        $peopleInformateurice->setUser($userInformateurice);
        $manager->persist($peopleInformateurice);

        // Adhérent.e user
        $output->writeln('         <comment>></comment> <info>Members creation...</info>');
        // adhe1
        $userAdherentE1 = new User();
        $userAdherentE1->setUsername('adhe1');

        $passwordAdherentE1 = $this->hasher->hashPassword($userAdherentE1, 'a');
        $userAdherentE1->setPassword($passwordAdherentE1);

        $peopleAdherentE1 = new People();

        $peopleAdherentE1->setDenomination($madame);
        $peopleAdherentE1->setFirstName('Jeanne');
        $peopleAdherentE1->setLastName('Vérany');
        $peopleAdherentE1->setEmailAddress('adherente1@fake.mail');

        $peopleAdherentE1Address = new Address('15 rue Mary Ann Weathers', null, '76200', 'Dieppe', 'France');
        $manager->persist($peopleAdherentE1Address);
        $peopleAdherentE1->addAddress($peopleAdherentE1Address);

        $peopleAdherentE1->setIsReceivingNewsletter(true);
        $peopleAdherentE1->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdherentE1->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE1->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE1->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdherentE1->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordAdherentE2 = $this->hasher->hashPassword($userAdherentE2, 'a');
        $userAdherentE2->setPassword($passwordAdherentE2);

        $peopleAdherentE2 = new People();

        $peopleAdherentE2->setDenomination($madame);
        $peopleAdherentE2->setFirstName('Arlette');
        $peopleAdherentE2->setLastName('Maurice');
        $peopleAdherentE2->setEmailAddress('adherente2@fake.mail');

        $peopleAdherentE2Address = new Address('25 rue Angela Davis', null, '93100', 'Montreuil', 'France');
        $manager->persist($peopleAdherentE2Address);
        $peopleAdherentE2->addAddress($peopleAdherentE2Address);

        $peopleAdherentE2->setIsReceivingNewsletter(true);
        $peopleAdherentE2->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdherentE2->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE2->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE2->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdherentE2->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordAdherentE3 = $this->hasher->hashPassword($userAdherentE3, 'a');
        $userAdherentE3->setPassword($passwordAdherentE3);

        $peopleAdherentE3 = new People();

        $peopleAdherentE3->setDenomination($docteur);
        $peopleAdherentE3->setFirstName('Ladislas');
        $peopleAdherentE3->setLastName('Bullion');
        $peopleAdherentE3->setEmailAddress('adherente3@fake.mail');

        $peopleAdherentE3Address = new Address('35 rue Barbara Smith', 'Appartement 874', '93200', 'Saint-Denis', 'France');
        $manager->persist($peopleAdherentE3Address);
        $peopleAdherentE3->addAddress($peopleAdherentE3Address);

        $peopleAdherentE3->setIsReceivingNewsletter(true);
        $peopleAdherentE3->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdherentE3->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE3->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE3->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdherentE3->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordAdherentE4 = $this->hasher->hashPassword($userAdherentE4, 'a');
        $userAdherentE4->setPassword($passwordAdherentE4);

        $peopleAdherentE4 = new People();

        $peopleAdherentE4->setDenomination($docteure);
        $peopleAdherentE4->setFirstName('Estelle');
        $peopleAdherentE4->setLastName('Lafaille');
        $peopleAdherentE4->setEmailAddress('adherente4@fake.mail');

        $peopleAdherentE4Address = new Address('45 rue Sojourner Truth', null, '97200', 'Fort-de-France', 'France');
        $manager->persist($peopleAdherentE4Address);
        $peopleAdherentE4->addAddress($peopleAdherentE4Address);

        $peopleAdherentE4->setIsReceivingNewsletter(false);
        $peopleAdherentE4->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdherentE4->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE4->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE4->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdherentE4->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordAdherentE5 = $this->hasher->hashPassword($userAdherentE5, 'a');
        $userAdherentE5->setPassword($passwordAdherentE5);

        $peopleAdherentE5 = new People();

        $peopleAdherentE5->setDenomination($docteure);
        $peopleAdherentE5->setFirstName('Agathe');
        $peopleAdherentE5->setLastName('Duval');
        $peopleAdherentE5->setEmailAddress('adherente5@fake.mail');

        $peopleAdherentE5Address = new Address('22 rue Harriet Tubman', null, '98713', 'Papeete', 'France');
        $manager->persist($peopleAdherentE5Address);
        $peopleAdherentE5->addAddress($peopleAdherentE5Address);

        $peopleAdherentE5->setIsReceivingNewsletter(true);
        $peopleAdherentE5->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleAdherentE5->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE5->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE5->setWorkPhoneNumber($workPhoneNumber);
        $peopleAdherentE5->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordAdherentE6 = $this->hasher->hashPassword($userAdherentE6, 'a');
        $userAdherentE6->setPassword($passwordAdherentE6);

        $peopleAdherentE6 = new People();

        $peopleAdherentE6->setDenomination($madame);
        $peopleAdherentE6->setFirstName('Christine');
        $peopleAdherentE6->setLastName('Rit');
        $peopleAdherentE6->setEmailAddress('adherente6@fake.mail');

        $peopleAdherentE6Address = new Address('141 rue Frances Harper', null, '97300', 'Cayenne', 'France');
        $manager->persist($peopleAdherentE6Address);
        $peopleAdherentE6->addAddress($peopleAdherentE6Address);

        $peopleAdherentE6->setIsReceivingNewsletter(true);
        $peopleAdherentE6->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE6->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE6->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE6->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE6->addResponsibility($roleAdherentE);
        $userAdherentE6->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE6);
        $peopleAdherentE6->setUser($userAdherentE6);
        $manager->persist($peopleAdherentE6);

        // adhe7
        $userAdherentE7 = new User();
        $userAdherentE7->setUsername('adhe7');

        $passwordAdherentE7 = $this->hasher->hashPassword($userAdherentE7, 'a');
        $userAdherentE7->setPassword($passwordAdherentE7);

        $peopleAdherentE7 = new People();

        $peopleAdherentE7->setDenomination($madame);
        $peopleAdherentE7->setFirstName('Maurelle');
        $peopleAdherentE7->setLastName('Lespérance');
        $peopleAdherentE7->setEmailAddress('adherente7@fake.mail');

        $peopleAdherentE7Address = new Address('13 rue Mary Church Terrel', null, '92000', 'Nanterre', 'France');
        $manager->persist($peopleAdherentE7Address);
        $peopleAdherentE7->addAddress($peopleAdherentE7Address);

        $peopleAdherentE7->setIsReceivingNewsletter(true);
        $peopleAdherentE7->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE7->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE7->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE7->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE7->addResponsibility($roleAdherentE);
        $userAdherentE7->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE7);
        $peopleAdherentE7->setUser($userAdherentE7);
        $manager->persist($peopleAdherentE7);

        // adhe8
        $userAdherentE8 = new User();
        $userAdherentE8->setUsername('adhe8');

        $passwordAdherentE8 = $this->hasher->hashPassword($userAdherentE8, 'a');
        $userAdherentE8->setPassword($passwordAdherentE8);

        $peopleAdherentE8 = new People();

        $peopleAdherentE8->setDenomination($madame);
        $peopleAdherentE8->setFirstName('Joseph');
        $peopleAdherentE8->setLastName('Lagueux');
        $peopleAdherentE8->setEmailAddress('adherente8@fake.mail');

        $peopleAdherentE8Address = new Address('56 rue Sylvia Rivera', null, '94400', 'Vitry-sur-Seine', 'France');
        $manager->persist($peopleAdherentE8Address);
        $peopleAdherentE8->addAddress($peopleAdherentE8Address);

        $peopleAdherentE8->setIsReceivingNewsletter(true);
        $peopleAdherentE8->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE8->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE8->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE8->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE8->addResponsibility($roleAdherentE);
        $userAdherentE8->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE8);
        $peopleAdherentE8->setUser($userAdherentE8);
        $manager->persist($peopleAdherentE8);

        // adhe9
        $userAdherentE9 = new User();
        $userAdherentE9->setUsername('adhe9');

        $passwordAdherentE9 = $this->hasher->hashPassword($userAdherentE9, 'a');
        $userAdherentE9->setPassword($passwordAdherentE9);

        $peopleAdherentE9 = new People();

        $peopleAdherentE9->setDenomination($madame);
        $peopleAdherentE9->setFirstName('Mirabelle');
        $peopleAdherentE9->setLastName('Totah');
        $peopleAdherentE9->setEmailAddress('adherente9@fake.mail');

        $peopleAdherentE9Address = new Address('47 rue Marsha P. Johnson', null, '69286', 'Rillieux-la-Pape', 'France');
        $manager->persist($peopleAdherentE9Address);
        $peopleAdherentE9->addAddress($peopleAdherentE9Address);

        $peopleAdherentE9->setIsReceivingNewsletter(true);
        $peopleAdherentE9->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE9->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE9->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE9->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE9->addResponsibility($roleAdherentE);
        $userAdherentE9->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE9);
        $peopleAdherentE9->setUser($userAdherentE9);
        $manager->persist($peopleAdherentE9);

        // adhe10
        $userAdherentE10 = new User();
        $userAdherentE10->setUsername('adhe10');

        $passwordAdherentE10 = $this->hasher->hashPassword($userAdherentE10, 'a');
        $userAdherentE10->setPassword($passwordAdherentE10);

        $peopleAdherentE10 = new People();

        $peopleAdherentE10->setDenomination($madame);
        $peopleAdherentE10->setFirstName('Myriam');
        $peopleAdherentE10->setLastName('Kouri');
        $peopleAdherentE10->setEmailAddress('adherente10@fake.mail');

        $peopleAdherentE10Address = new Address('198 rue Josephine Baker', null, '78146', 'Chatou', 'France');
        $manager->persist($peopleAdherentE10Address);
        $peopleAdherentE10->addAddress($peopleAdherentE10Address);

        $peopleAdherentE10->setIsReceivingNewsletter(true);
        $peopleAdherentE10->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE10->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE10->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE10->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE10->addResponsibility($roleAdherentE);
        $userAdherentE10->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE10);
        $peopleAdherentE10->setUser($userAdherentE10);
        $manager->persist($peopleAdherentE10);

        // adhe11
        $userAdherentE11 = new User();
        $userAdherentE11->setUsername('adhe11');

        $passwordAdherentE11 = $this->hasher->hashPassword($userAdherentE11, 'a');
        $userAdherentE11->setPassword($passwordAdherentE11);

        $peopleAdherentE11 = new People();

        $peopleAdherentE11->setDenomination($madame);
        $peopleAdherentE11->setFirstName('Wei');
        $peopleAdherentE11->setLastName('Ch\'eng');
        $peopleAdherentE11->setEmailAddress('adherente11@fake.mail');

        $peopleAdherentE11Address = new Address('456 rue Karl Heinrich Ulrichs', null, '62510', 'Liévin', 'France');
        $manager->persist($peopleAdherentE11Address);
        $peopleAdherentE11->addAddress($peopleAdherentE11Address);

        $peopleAdherentE11->setIsReceivingNewsletter(true);
        $peopleAdherentE11->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE11->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE11->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE11->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE11->addResponsibility($roleAdherentE);
        $userAdherentE11->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE11);
        $peopleAdherentE11->setUser($userAdherentE11);
        $manager->persist($peopleAdherentE11);

        // adhe12
        $userAdherentE12 = new User();
        $userAdherentE12->setUsername('adhe12');

        $passwordAdherentE12 = $this->hasher->hashPassword($userAdherentE12, 'a');
        $userAdherentE12->setPassword($passwordAdherentE12);

        $peopleAdherentE12 = new People();

        $peopleAdherentE12->setDenomination($mix);
        $peopleAdherentE12->setFirstName('Jaska');
        $peopleAdherentE12->setLastName('Reho');
        $peopleAdherentE12->setEmailAddress('adherente12@fake.mail');

        $peopleAdherentE12Address = new Address('2 rue Michael Dillon', null, '97103', 'Baie-Mahault', 'France');
        $manager->persist($peopleAdherentE12Address);
        $peopleAdherentE12->addAddress($peopleAdherentE12Address);

        $peopleAdherentE12->setIsReceivingNewsletter(true);
        $peopleAdherentE12->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE12->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE12->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE12->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE12->addResponsibility($roleAdherentE);
        $userAdherentE12->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE12);
        $peopleAdherentE12->setUser($userAdherentE12);
        $manager->persist($peopleAdherentE12);

        // adhe13
        $userAdherentE13 = new User();
        $userAdherentE13->setUsername('adhe13');

        $passwordAdherentE13 = $this->hasher->hashPassword($userAdherentE13, 'a');
        $userAdherentE13->setPassword($passwordAdherentE13);

        $peopleAdherentE13 = new People();

        $peopleAdherentE13->setDenomination($madame);
        $peopleAdherentE13->setFirstName('Arbi');
        $peopleAdherentE13->setLastName('Musliyevich');
        $peopleAdherentE13->setEmailAddress('adherente13@fake.mail');

        $peopleAdherentE13Address = new Address('184 rue Virginia Woolf', null, '62498', 'Lens', 'France');
        $manager->persist($peopleAdherentE13Address);
        $peopleAdherentE13->addAddress($peopleAdherentE13Address);

        $peopleAdherentE13->setIsReceivingNewsletter(true);
        $peopleAdherentE13->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE13->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE13->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE13->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE13->addResponsibility($roleAdherentE);
        $userAdherentE13->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE13);
        $peopleAdherentE13->setUser($userAdherentE13);
        $manager->persist($peopleAdherentE13);

        // adhe14
        $userAdherentE14 = new User();
        $userAdherentE14->setUsername('adhe14');

        $passwordAdherentE14 = $this->hasher->hashPassword($userAdherentE14, 'a');
        $userAdherentE14->setPassword($passwordAdherentE14);

        $peopleAdherentE14 = new People();

        $peopleAdherentE14->setDenomination($madame);
        $peopleAdherentE14->setFirstName('Hana');
        $peopleAdherentE14->setLastName('Moravcová');
        $peopleAdherentE14->setEmailAddress('adherente14@fake.mail');

        $peopleAdherentE14Address = new Address('123 rue Bayard Rustin', null, '88160', 'Épinal', 'France');
        $manager->persist($peopleAdherentE14Address);
        $peopleAdherentE14->addAddress($peopleAdherentE14Address);

        $peopleAdherentE14->setIsReceivingNewsletter(true);
        $peopleAdherentE14->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE14->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE14->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE14->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE14->addResponsibility($roleAdherentE);
        $userAdherentE14->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE14);
        $peopleAdherentE14->setUser($userAdherentE14);
        $manager->persist($peopleAdherentE14);

        // adhe15
        $userAdherentE15 = new User();
        $userAdherentE15->setUsername('adhe15');

        $passwordAdherentE15 = $this->hasher->hashPassword($userAdherentE15, 'a');
        $userAdherentE15->setPassword($passwordAdherentE15);

        $peopleAdherentE15 = new People();

        $peopleAdherentE15->setDenomination($madame);
        $peopleAdherentE15->setFirstName('Renata');
        $peopleAdherentE15->setLastName('Milić');
        $peopleAdherentE15->setEmailAddress('adherente15@fake.mail');

        $peopleAdherentE15Address = new Address('1 rue Frida Kahlo', null, '86066', 'Châtellerault', 'France');
        $manager->persist($peopleAdherentE15Address);
        $peopleAdherentE15->addAddress($peopleAdherentE15Address);

        $peopleAdherentE15->setIsReceivingNewsletter(true);
        $peopleAdherentE15->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $peopleAdherentE15->setHomePhoneNumber($homePhoneNumber);
        $peopleAdherentE15->setCellPhoneNumber($cellPhoneNumber);
        $peopleAdherentE15->setWorkPhoneNumber($workPhoneNumber);

        $userAdherentE15->addResponsibility($roleAdherentE);
        $userAdherentE15->addResponsibility($roleInscritE);

        $manager->persist($userAdherentE15);
        $peopleAdherentE15->setUser($userAdherentE15);
        $manager->persist($peopleAdherentE15);

        // supportive user
        $output->writeln('         <comment>></comment> <info>Support users creation...</info>');
        $userSupportive = new User();
        $userSupportive->setUsername('supp1');

        $passwordSupportive = $this->hasher->hashPassword($userSupportive, 'a');
        $userSupportive->setPassword($passwordSupportive);

        $peopleSupportive = new People();

        $peopleSupportive->setDenomination($docteure);
        $peopleSupportive->setFirstName('Morgane');
        $peopleSupportive->setLastName('Cauw');
        $peopleSupportive->setEmailAddress('supportive@fake.mail');

        $peopleSupportiveAddress = new Address('127 rue Ada Lovelace', null, '98805', 'Dumbéa', 'France');
        $manager->persist($peopleSupportiveAddress);
        $peopleSupportive->addAddress($peopleSupportiveAddress);

        $peopleSupportive->setIsReceivingNewsletter(true);
        $peopleSupportive->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleSupportive->setHomePhoneNumber($homePhoneNumber);
        $peopleSupportive->setCellPhoneNumber($cellPhoneNumber);
        $peopleSupportive->setWorkPhoneNumber($workPhoneNumber);
        $peopleSupportive->setWorkFaxNumber($workFaxPhoneNumber);
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

        $passwordRich = $this->hasher->hashPassword($userRich, 'a');
        $userRich->setPassword($passwordRich);

        $peopleRich = new People();

        $peopleRich->setDenomination($docteure);
        $peopleRich->setFirstName('Elliot');
        $peopleRich->setLastName('Kronem');
        $peopleRich->setEmailAddress('rich@fake.mail');

        $peopleRichAddress = new Address('12 Avenue des Champs-Élysées', null, '75008', 'Paris', 'France');
        $manager->persist($peopleRichAddress);
        $peopleRich->addAddress($peopleRichAddress);
        $peopleRich->addAddress($peopleAdherentE5Address);

        $peopleRich->setIsReceivingNewsletter(true);
        $peopleRich->setNewsletterDematerialization(false);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleRich->setHomePhoneNumber($homePhoneNumber);
        $peopleRich->setCellPhoneNumber($cellPhoneNumber);
        $peopleRich->setWorkPhoneNumber($workPhoneNumber);
        $peopleRich->setWorkFaxNumber($workFaxPhoneNumber);
        $peopleRich->setObservations('A donné un chèque de 8000€ au gala de 2018.');
        $peopleRich->setSensitiveObservations('');

        $userRich->addResponsibility($roleMecene);
        $userRich->addResponsibility($roleInscritE);

        $manager->persist($userRich);
        $peopleRich->setUser($userRich);
        $manager->persist($peopleRich);

        // Inscrite uniquement
        $output->writeln('         <comment>></comment> <info>Only signed up users creation...</info>');
        $userRegistered = new User();
        $userRegistered ->setUsername('inscr');

        $passwordRegistered = $this->hasher->hashPassword($userRegistered, 'a');
        $userRegistered->setPassword($passwordRegistered);

        $peopleRegistered = new People();

        $peopleRegistered->setDenomination($madame);
        $peopleRegistered->setFirstName('Jodi');
        $peopleRegistered->setLastName('Manchon');
        $peopleRegistered->setEmailAddress('inscrite@fake.mail');

        $peopleRegisteredAddress = new Address('22 rue des écureuils', null, '13001', 'Marseille', 'France');
        $manager->persist($peopleRegisteredAddress);
        $peopleRegistered->addAddress($peopleRegisteredAddress);

        $peopleRegistered->setIsReceivingNewsletter(true);
        $peopleRegistered->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleRegistered->setHomePhoneNumber($homePhoneNumber);
        $peopleRegistered->setCellPhoneNumber($cellPhoneNumber);
        $peopleRegistered->setWorkPhoneNumber($workPhoneNumber);
        $peopleRegistered->setWorkFaxNumber($workFaxPhoneNumber);
        $peopleRegistered->setObservations('Notée suite à appel téléphonique du 23 juillet 2019.');
        $peopleRegistered->setSensitiveObservations('');

        $userRegistered->addResponsibility($roleInscritE);

        $manager->persist($userRegistered);
        $peopleRegistered->setUser($userRegistered);
        $manager->persist($peopleRegistered);

        // test
        $userTest = new User();
        $userTest ->setUsername('test');

        $passwordTest = $this->hasher->hashPassword($userTest, 'a');
        $userTest->setPassword($passwordTest);

        $peopleTest = new People();

        $peopleTest->setDenomination($madame);
        $peopleTest->setFirstName('Mélissa');
        $peopleTest->setLastName('Dubois');
        $peopleTest->setEmailAddress('administrator-life@fake.mail');

        $peopleTestAddress = new Address('103 rue Ifti Nasim', null, '97408', 'La Possession', 'France');
        $manager->persist($peopleTestAddress);
        $peopleTest->addAddress($peopleTestAddress);

        $peopleTest->setIsReceivingNewsletter(true);
        $peopleTest->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleTest->setHomePhoneNumber($homePhoneNumber);
        $peopleTest->setCellPhoneNumber($cellPhoneNumber);
        $peopleTest->setWorkPhoneNumber($workPhoneNumber);
        $peopleTest->setWorkFaxNumber($workFaxPhoneNumber);
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

        $password = $this->hasher->hashPassword($userTest2, 'a');
        $userTest2->setPassword($password);

        $peopleTest2 = new People();

        $peopleTest2->setDenomination($madame);
        $peopleTest2->setFirstName('Jeanne');
        $peopleTest2->setLastName('Carton');
        $peopleTest2->setEmailAddress('administrator-life@fake.mail');

        $peopleTest2Address = new Address('10 rue des catacombes', null, '97610', 'Koungou', 'France');
        $manager->persist($peopleTest2Address);
        $peopleTest2->addAddress($peopleTest2Address);

        $peopleTest2->setIsReceivingNewsletter(true);
        $peopleTest2->setNewsletterDematerialization(true);
        $homePhoneNumber = '0' . rand(111111111, 499999999);
        $cellPhoneNumber = '0' . rand(611111111, 799999999);
        $workPhoneNumber = '0' . rand(111111111, 799999999);
        $workFaxPhoneNumber = '0' . rand(111111111, 499999999);
        $peopleTest2->setHomePhoneNumber($homePhoneNumber);
        $peopleTest2->setCellPhoneNumber($cellPhoneNumber);
        $peopleTest2->setWorkPhoneNumber($workPhoneNumber);
        $peopleTest2->setWorkFaxNumber($workFaxPhoneNumber);
        $peopleTest2->setObservations(':3c');
        $peopleTest2->setSensitiveObservations('RAS');

        $userTest2->addResponsibility($roleAdmin);
        $userTest2->addResponsibility($roleInscritE);

        $manager->persist($userTest2);
        $peopleTest2->setUser($userTest2);
        $manager->persist($peopleTest2);

        // Final flush
        $manager->flush();
        $output->writeln('      <comment>></comment> <info>Users creation complete</info>');
    }
}
