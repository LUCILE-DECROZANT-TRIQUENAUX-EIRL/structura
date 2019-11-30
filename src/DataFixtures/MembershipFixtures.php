<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\People;
use App\Entity\Membership;
use App\Entity\MembershipType;
use App\Entity\Payment;
use App\Entity\PaymentType;
// use App\Repository\PaymentTypeRepository;
// use App\Repository\MembershipTypeRepository;
// use App\Repository\PeopleRepository;


class MembershipFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct()
    {
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }

    public function load(ObjectManager $manager)
    {
        // Creating MembershipType
        $MembershipTypeFamily = new MembershipType();
        $MembershipTypeFamily->setDefaultAmount(30.0);
        $MembershipTypeFamily->setLabel('Famille');
        $MembershipTypeFamily->setDescription('Adhésion d\'une famille à l\'association pour une année.');
        $MembershipTypeFamily->setIsMultiMembers(true);
        $MembershipTypeFamily->setNumberMaxMembers(2);

        $MembershipTypeRegular = new MembershipType();
        $MembershipTypeRegular->setDefaultAmount(20.0);
        $MembershipTypeRegular->setLabel('Normale');
        $MembershipTypeRegular->setDescription('Adhésion d\'une personne à l\'association pour une année.');
        $MembershipTypeRegular->setIsMultiMembers(false);
        $MembershipTypeRegular->setNumberMaxMembers(1);

        $manager->persist($MembershipTypeFamily);
        $manager->persist($MembershipTypeRegular);

        // Retreiving PaymentType from DB
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);
        $paymentTypeCash = $paymentTypeRepository->findOneBy(['label' => 'Espèces']);
        $paymentTypeCard = $paymentTypeRepository->findOneBy(['label' => 'Carte Bleue']);
        $paymentTypeTransfer = $paymentTypeRepository->findOneBy(['label' => 'Virement']);
        $paymentTypeCheck = $paymentTypeRepository->findOneBy(['label' => 'Chèque']);
        $paymentTypeHelloAsso = $paymentTypeRepository->findOneBy(['label' => 'HelloAsso']);


        // Retreiving adherent.e.s from DB
        $peopleRepository = $manager->getRepository(People::class);

        // adhe1
        $peopleAdherentE1 = $peopleRepository->findOneBy([
            'firstName' => 'Jeanne',
            'lastName' => 'Vérany'
        ]);
        // adhe2
        $peopleAdherentE2 = $peopleRepository->findOneBy([
            'firstName' => 'Arlette',
            'lastName' => 'Maurice'
        ]);
        // adhe3
        $peopleAdherentE3 = $peopleRepository->findOneBy([
            'firstName' => 'Ladislas',
            'lastName' => 'Bullion'
        ]);

        // -- Payments -- //

        // First payment
        $paymentAdhesionCheque50 = new Payment();

        $paymentAdhesionCheque50->setAmount(50);
        $paymentAdhesionCheque50->setType($paymentTypeCheck);

        $manager->persist($paymentAdhesionCheque50);

        // Second payment
        $paymentAdhesionHelloAsso30 = new Payment();

        $paymentAdhesionHelloAsso30->setAmount(30);
        $paymentAdhesionHelloAsso30->setType($paymentTypeHelloAsso);
        $paymentAdhesionHelloAsso30->setDateReceived(new \DateTime());
        $paymentAdhesionHelloAsso30->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionHelloAsso30);

        // Third payment
        $paymentAdhesionHelloAsso20 = new Payment();

        $paymentAdhesionHelloAsso20->setAmount(20);
        $paymentAdhesionHelloAsso20->setType($paymentTypeHelloAsso);
        $paymentAdhesionHelloAsso20->setDateReceived(new \DateTime());
        $paymentAdhesionHelloAsso20->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionHelloAsso20);

        // Date managment
        $plusOneYear = new \DateInterval('P1Y');
        $removeOneYear = clone $plusOneYear;
        $removeOneYear->invert = 1;

        $now = new \DateTime();

        $inOneYear = clone $now;
        $inOneYear->add($plusOneYear);

        $lastYear = clone $now;
        $lastYear->add($removeOneYear);

        // -- Memberships -- //
        // Normal 1
        $membershipNormal1 = new Membership();

        $membershipNormal1
            ->setAmount(20)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionCheque50)
            ->setType($MembershipTypeRegular);

        $manager->persist($membershipNormal1);

        // Normal 2
        $membershipNormal2 = new Membership();

        $membershipNormal2
            ->setAmount(20)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionHelloAsso20)
            ->setType($MembershipTypeRegular);

        $manager->persist($membershipNormal2);

        // Family
        $membershipFamily = new Membership();

        $membershipFamily->setAmount(30);
        $membershipFamily->setDateStart($now);
        $membershipFamily->setDateEnd($inOneYear);
        $membershipFamily->setPayment($paymentAdhesionHelloAsso30);
        $membershipFamily->setType($MembershipTypeFamily);

        $manager->persist($membershipFamily);

        // Adding the memberships to the people
        $peopleAdherentE1->addMembership($membershipNormal2);
        $peopleAdherentE2->addMembership($membershipFamily);
        $peopleAdherentE3->addMembership($membershipNormal1);
        $peopleAdherentE3->addMembership($membershipFamily);

        // people persist
        $manager->persist($peopleAdherentE1);
        $manager->persist($peopleAdherentE2);
        $manager->persist($peopleAdherentE3);

        // Final flush
        $manager->flush();
    }
}
