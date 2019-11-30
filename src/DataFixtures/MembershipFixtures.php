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

        // Creating MembershipType
        $famille = new MembershipType();
        $famille->setDefaultAmount(30.0);
        $famille->setLabel('Famille');
        $famille->setDescription('Adhésion d\'une personne à l\'association pour une année.');
        $famille->setIsMultiMembers(true);
        $famille->setNumberMaxMembers(2);

        $normale = new MembershipType();
        $normale->setDefaultAmount(20.0);
        $normale->setLabel('Normale');
        $normale->setDescription('Adhésion d\'une famille à l\'association pour une année.');
        $normale->setIsMultiMembers(false);
        $normale->setNumberMaxMembers(1);

        $manager->persist($famille);
        $manager->persist($normale);

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
        $paymentAdhesionCheque50 = new Payment();

        $paymentAdhesionCheque50->setAmount(50);
        $paymentAdhesionCheque50->setType($cheque);

        $manager->persist($paymentAdhesionCheque50);

        // Second payment
        $paymentAdhesionHelloAsso30 = new Payment();

        $paymentAdhesionHelloAsso30->setAmount(30);
        $paymentAdhesionHelloAsso30->setType($helloAsso);
        $paymentAdhesionHelloAsso30->setDateReceived(new \DateTime());
        $paymentAdhesionHelloAsso30->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionHelloAsso30);

        // Third payment
        $paymentAdhesionHelloAsso20 = new Payment();

        $paymentAdhesionHelloAsso20->setAmount(20);
        $paymentAdhesionHelloAsso20->setType($helloAsso);
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
            ->setType($normale);

        $manager->persist($membershipNormal1);

        // Normal 2
        $membershipNormal2 = new Membership();

        $membershipNormal2
            ->setAmount(20)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionHelloAsso20)
            ->setType($normale);

        $manager->persist($membershipNormal2);

        // Family
        $membershipFamily = new Membership();

        $membershipFamily->setAmount(30);
        $membershipFamily->setDateStart($now);
        $membershipFamily->setDateEnd($inOneYear);
        $membershipFamily->setPayment($paymentAdhesionHelloAsso30);
        $membershipFamily->setType($famille);

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
