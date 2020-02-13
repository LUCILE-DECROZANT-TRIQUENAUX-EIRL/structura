<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Responsibility;
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
        /***************************************************/
        /*  Responsibilities are created during migration  */
        /***************************************************/
        $responsibilityRepository = $manager->getRepository(Responsibility::class);
        $translationRepository = $manager->getRepository('Gedmo\\Translatable\\Entity\\Translation');

        $roleAdmin = $responsibilityRepository->findOneBy(['code' => 'ROLE_ADMIN']);
        // $roleAdminSensible = $responsibilityRepository->findOneBy(['code' => 'ROLE_ADMIN_SENSIBLE']);
        // $roleGestion = $responsibilityRepository->findOneBy(['code' => 'ROLE_GESTION']);
        // $roleGestionSensible = $responsibilityRepository->findOneBy(['code' => 'ROLE_GESTION_SENSIBLE']);
        // $roleInformateurice = $responsibilityRepository->findOneBy(['code' => 'ROLE_INFORMATEURICE']);
        // $roleAdherentE = $responsibilityRepository->findOneBy(['code' => 'ROLE_ADHERENT_E']);
        // $roleExAdherentE = $responsibilityRepository->findOneBy(['code' => 'ROLE_EX_ADHERENT_E']);
        // $roleMecene = $responsibilityRepository->findOneBy(['code' => 'ROLE_MECENE']);
        // $roleSympathisantE = $responsibilityRepository->findOneBy(['code' => 'ROLE_SYMPATHISANT_E']);
        // $roleConsultationAnnuaire = $responsibilityRepository->findOneBy(['code' => 'ROLE_CONSULTATION_ANNUAIRE']);
        // $roleInscritE = $responsibilityRepository->findOneBy(['code' => 'ROLE_INSCRIT_E']);

        // Admin
        $translationRepository
            ->translate($roleAdmin, 'label', 'fr', 'Administrateurice de la base de données')
            ->translate($roleAdmin, 'description', 'fr', 'Peut consulter ou restaurer les données archivées non sensibles.')
            ->translate($roleAdmin, 'label', 'en', 'Database administrator')
            ->translate($roleAdmin, 'description', 'en', 'Can read or restore archived non-sensitive data.');

        // Final flush
        $manager->flush();
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
