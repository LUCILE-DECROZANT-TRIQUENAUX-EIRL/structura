<?php

namespace App\DataFixtures;

use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Responsibility;
use App\Entity\MembershipType;
use App\Entity\PaymentType;
use App\Entity\Denomination;
use App\Repository\ResponsibilityRepository;
use Gedmo\Translatable\Translatable;
use Gedmo\Translatable\Entity\Translation;

class TranslationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function __construct()
    {

    }

    public static function getGroups(): array
    {
        return ['translation'];
    }

    public function load(ObjectManager $manager)
    {
        // Create logger used to display information messages
        $output = new ConsoleOutput();

        /***************************************************/
        /*  Responsibilities are created during migration  */
        /***************************************************/
        $responsibilityRepository = $manager->getRepository(Responsibility::class);
        $translationRepository = $manager->getRepository('Gedmo\\Translatable\\Entity\\Translation');

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

        // Start of the translation
        /**********************************
         ******** RESPONSIBILITIES ********
         **********************************/
        $output->writeln('      <comment>></comment> <info>Responsibilities translation...</info>');
        // Admin
        $translationRepository
                ->translate($roleAdmin, 'label', 'fr', 'Administrateurice de la base de données')
                ->translate($roleAdmin, 'description', 'fr', 'Peut consulter ou restaurer les données archivées non sensibles.')
                ->translate($roleAdmin, 'label', 'en', 'Database administrator')
                ->translate($roleAdmin, 'description', 'en', 'Can read or restore archived non-sensitive data.');

        // AdminSensible
        $translationRepository
                ->translate($roleAdminSensible, 'label', 'fr', 'Administrateurice des données sensibles')
                ->translate($roleAdminSensible, 'description', 'fr', 'Peut consulter ou restaurer les données sensibles archivées.')
                ->translate($roleAdminSensible, 'label', 'en', 'Sensitive data administrator')
                ->translate($roleAdminSensible, 'description', 'en', 'Can read or restore archived sensitive data.');

        // Gestion
        $translationRepository
                ->translate($roleGestion, 'label', 'fr', 'Gestionnaire')
                ->translate($roleGestion, 'description', 'fr', 'Permet d\'afficher, éditer, supprimer les données non sensibles d\'autres comptes, de créer des comptes utilisateurice, d\'éditer les rôles d\'autres comptes (mis à part les rôles sensibles) et de consulter, modifier et supprimer des informations dans l\'annuaire des professionnels de santé.')
                ->translate($roleGestion, 'label', 'en', 'Manager')
                ->translate($roleGestion, 'description', 'en', 'Allows to view, edit, delete non-sensitive data from other accounts, create user accounts, edit roles of other accounts (except for sensitive roles), view, modify and delete information in the Healthcare Professionals Directory.');

        // GestionSensible
        $translationRepository
                ->translate($roleGestionSensible, 'label', 'fr', 'Gestionnaire des données sensibles')
                ->translate($roleGestionSensible, 'description', 'fr', 'Permet d\'afficher, éditer, supprimer les données sensibles d\'autres comptes et d\'éditer les rôles liés aux données sensibles.')
                ->translate($roleGestionSensible, 'label', 'en', 'Sensitive data manager')
                ->translate($roleGestionSensible, 'description', 'en', 'Allows to view, edit, delete sensitive data from other accounts and edit roles related to sensitive data.');

        // Informateurice
        $translationRepository
                ->translate($roleInformateurice, 'label', 'fr', 'Informateurice')
                ->translate($roleInformateurice, 'description', 'fr', 'Permet de créer, afficher, éditer et supprimer un événement ou une newsletter et d\'envoyer les newsletters.')
                ->translate($roleInformateurice, 'label', 'en', 'Informant')
                ->translate($roleInformateurice, 'description', 'en', 'Allows to create, view, edit, delete an event or newsletter and to send newsletters.');

        // AdherentE
        $translationRepository
                ->translate($roleAdherentE, 'label', 'fr', 'Adhérent.e')
                ->translate($roleAdherentE, 'description', 'fr', 'Permet de recevoir la newsletter, les convocations à l\'AG, les invitations aux événements, de consulter les documents des AG des années de cotisation, de voir les événements \"privés\" et de renouveler son adhésion.')
                ->translate($roleAdherentE, 'label', 'en', 'Member')
                ->translate($roleAdherentE, 'description', 'en', 'Allows to receive the newsletter, invitations to GAs, invitations to events, to consult documents of GAs of the years of contribution, to see \"private\" events and to renew membership.');

        // ExAdherentE
        $translationRepository
                ->translate($roleExAdherentE, 'label', 'fr', 'Ex-adhérent.e')
                ->translate($roleExAdherentE, 'description', 'fr', 'Permet de recevoir une relance pour adhérer à l\'association, renouveler son adhésion et consulter les documents des AG des années de cotisation.')
                ->translate($roleExAdherentE, 'label', 'en', 'Ex-member')
                ->translate($roleExAdherentE, 'description', 'en', 'Allows to receive a reminder to join the association, renew membership and consult documents of GAs of the years of contribution.');

        // Mecene
        $translationRepository
                ->translate($roleMecene, 'label', 'fr', 'Mécène')
                ->translate($roleMecene, 'description', 'fr', 'Peut faire des dons.')
                ->translate($roleMecene, 'label', 'en', 'Patron')
                ->translate($roleMecene, 'description', 'en', 'Can make donations.');

        // SympathisantE
        $translationRepository
                ->translate($roleSympathisantE, 'label', 'fr', 'Sympathisant.e')
                ->translate($roleSympathisantE, 'description', 'fr', 'Peut recevoir la newsletter et adhérer à l\'association.')
                ->translate($roleSympathisantE, 'label', 'en', 'Supporter')
                ->translate($roleSympathisantE, 'description', 'en', 'Can receive the newsletter and join the association.');

        // ConsultationAnnuaire
        $translationRepository
                ->translate($roleConsultationAnnuaire, 'label', 'fr', 'Consultation de l\'annuaire')
                ->translate($roleConsultationAnnuaire, 'description', 'fr', 'Donne l\'accès à la consultation de l\'annuaire des professionnels de santé.')
                ->translate($roleConsultationAnnuaire, 'label', 'en', 'See directory')
                ->translate($roleConsultationAnnuaire, 'description', 'en', 'Gives access to the Health Care Professionals directory.');

        // InscritE
        $translationRepository
                ->translate($roleInscritE, 'label', 'fr', 'Inscrit.e')
                ->translate($roleInscritE, 'description', 'fr', 'Permet de voir les informations de son compte, de les éditer, de les archiver et de demander l\'accès à l\'annuaire des professionels de santé.')
                ->translate($roleInscritE, 'label', 'en', 'Registered')
                ->translate($roleInscritE, 'description', 'en', 'Allows to view,edit, archive your account information, and request access to the Health Care Professionals directory.');

        /**********************************
         *********** MEMBERSHIP ***********
         **********************************/
        // Retreiving Membership types from DB
        $membershipTypeRepository = $manager->getRepository(MembershipType::class);
        $membershipTypeFamily     = $membershipTypeRepository->findOneBy(['label' => 'Famille']);
        $MembershipTypeRegular    = $membershipTypeRepository->findOneBy(['label' => 'Normale']);

        $output->writeln('      <comment>></comment> <info>Membership translation...</info>');
        // Family
        $translationRepository
                ->translate($membershipTypeFamily, 'label', 'fr', 'Famille')
                ->translate($membershipTypeFamily, 'label', 'en', 'Family');

        // Regular
        $translationRepository
                ->translate($MembershipTypeRegular, 'label', 'fr', 'Normal')
                ->translate($MembershipTypeRegular, 'label', 'en', 'Regular');

        /**********************************
         ********** PAYMENT TYPE **********
         **********************************/
        // Retreiving PaymentType from DB
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);
        $paymentTypeCash       = $paymentTypeRepository->findOneBy(['label' => 'Espèces']);
        $paymentTypeCard       = $paymentTypeRepository->findOneBy(['label' => 'Carte Bleue']);
        $paymentTypeTransfer   = $paymentTypeRepository->findOneBy(['label' => 'Virement']);
        $paymentTypeCheck      = $paymentTypeRepository->findOneBy(['label' => 'Chèque']);
        $paymentTypeHelloAsso  = $paymentTypeRepository->findOneBy(['label' => 'HelloAsso']);

        $output->writeln('      <comment>></comment> <info>Payment type translation...</info>');
        // Cash
        $translationRepository
                ->translate($paymentTypeCash, 'label', 'fr', 'Espèces')
                ->translate($paymentTypeCash, 'label', 'en', 'Cash');

        // Card
        $translationRepository
                ->translate($paymentTypeCard, 'label', 'fr', 'Carte Bleue')
                ->translate($paymentTypeCard, 'label', 'en', 'Card');

        // Transfer
        $translationRepository
                ->translate($paymentTypeTransfer, 'label', 'fr', 'Virement')
                ->translate($paymentTypeTransfer, 'label', 'en', 'Transfer');

        // Check
        $translationRepository
                ->translate($paymentTypeCheck, 'label', 'fr', 'Chèque')
                ->translate($paymentTypeCheck, 'label', 'en', 'Check');

        // Hello Asso
        $translationRepository
                ->translate($paymentTypeHelloAsso, 'label', 'fr', 'HelloAsso')
                ->translate($paymentTypeHelloAsso, 'label', 'en', 'HelloAsso');

        /**********************************
         ******* DENOMINATION TYPE ********
         **********************************/
        // Retreiving Denominations from DB
        $denominationRepository = $manager->getRepository(Denomination::class);
        $denominationMonsieur   = $denominationRepository->findOneBy(['label' => 'Monsieur']);
        $denominationMadame     = $denominationRepository->findOneBy(['label' => 'Madame']);
        $denominationDocteure   = $denominationRepository->findOneBy(['label' => 'Docteure']);
        $denominationDocteur    = $denominationRepository->findOneBy(['label' => 'Docteur']);

        $output->writeln('      <comment>></comment> <info>Denomination translation...</info>');
        // Monsieur
        $translationRepository
                ->translate($denominationMonsieur, 'label', 'fr', 'Monsieur')
                ->translate($denominationMonsieur, 'label', 'en', 'Mister');

        // Madame
        $translationRepository
                ->translate($denominationMadame, 'label', 'fr', 'Madame')
                ->translate($denominationMadame, 'label', 'en', 'Miss');

        // Docteure
        $translationRepository
                ->translate($denominationDocteure, 'label', 'fr', 'Doctor')
                ->translate($denominationDocteure, 'label', 'en', 'Doctor');

        // Docteur
        $translationRepository
                ->translate($denominationDocteur, 'label', 'fr', 'Doctor')
                ->translate($denominationDocteur, 'label', 'en', 'Doctor');

        // Final flush
        $manager->flush();
        $output->writeln('      <comment>></comment> <info>Translation complete</info>');
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            MembershipFixtures::class,
            DonationFixtures::class,
        );
    }

}
