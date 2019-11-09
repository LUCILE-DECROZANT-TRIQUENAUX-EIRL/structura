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
        // Retreiving MembershipType from DB
        $membershipTypeRepository = $manager->getRepository(MembershipType::class);
        $normale = $membershipTypeRepository->findOneBy(['label' => 'Normale']);
        $famille = $membershipTypeRepository->findOneBy(['label' => 'Famille']);


        // Retreiving PaymentType from DB
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);
        $especes = $paymentTypeRepository->findOneBy(['label' => 'Espèces']);
        $cb = $paymentTypeRepository->findOneBy(['label' => 'Carte Bleue']);
        $virement = $paymentTypeRepository->findOneBy(['label' => 'Virement']);
        $cheque = $paymentTypeRepository->findOneBy(['label' => 'Chèque']);
        $helloAsso = $paymentTypeRepository->findOneBy(['label' => 'HelloAsso']);


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
        $paymentAdhesion1 = new Payment();

        $paymentAdhesion1->setAmount(30);
        $paymentAdhesion1->setType($cheque);

        $manager->persist($paymentAdhesion1);

        // Second payment
        $paymentAdhesion2 = new Payment();

        $paymentAdhesion2->setAmount(50);
        $paymentAdhesion2->setType($helloAsso);

        $manager->persist($paymentAdhesion2);

        // Third payment
        $paymentAdhesion3 = new Payment();

        $paymentAdhesion3->setAmount(30);
        $paymentAdhesion3->setType($helloAsso);

        $manager->persist($paymentAdhesion3);

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
            ->setAmount(30)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesion1)
            ->setType($normale);

        $manager->persist($membershipNormal1);

        // Normal 2
        $membershipNormal2 = new Membership();

        $membershipNormal2
            ->setAmount(30)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesion3)
            ->setType($normale);

        $manager->persist($membershipNormal2);

        // Family
        $membershipFamily = new Membership();

        $membershipFamily->setAmount(50);
        $membershipFamily->setDateStart($now);
        $membershipFamily->setDateEnd($inOneYear);
        $membershipFamily->setPayment($paymentAdhesion2);
        $membershipFamily->setType($famille);

        $manager->persist($membershipFamily);

        // Adding the memberships to the peoples
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
