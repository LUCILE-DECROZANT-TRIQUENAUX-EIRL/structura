<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
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


class MembershipFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct()
    {
    }

    public static function getGroups(): array
    {
        return ['memberships'];
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

        // First payment (check)
        $paymentAdhesionCheque50 = new Payment();
        $paymentAdhesionCheque50->setAmount(50);
        $paymentAdhesionCheque50->setType($paymentTypeCheck);

        $manager->persist($paymentAdhesionCheque50);

        // Second payment (hello asso)
        $paymentAdhesionHelloAsso30 = new Payment();
        $paymentAdhesionHelloAsso30->setAmount(30);
        $paymentAdhesionHelloAsso30->setType($paymentTypeHelloAsso);
        $paymentAdhesionHelloAsso30->setDateReceived(new \DateTime());
        $paymentAdhesionHelloAsso30->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionHelloAsso30);

        // Third payment (cash)
        $paymentAdhesionCash20 = new Payment();
        $paymentAdhesionCash20->setAmount(20);
        $paymentAdhesionCash20->setType($paymentTypeCash);
        $paymentAdhesionCash20->setDateReceived(new \DateTime());
        $paymentAdhesionCash20->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionCash20);

        // Fourth payment (card)
        $paymentAdhesionCard20 = new Payment();
        $paymentAdhesionCard20->setAmount(20);
        $paymentAdhesionCard20->setType($paymentTypeCard);
        $paymentAdhesionCard20->setDateReceived(new \DateTime());
        $paymentAdhesionCard20->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionCard20);

        // Fifth payment (transfer)
        $paymentAdhesionTransfer20 = new Payment();
        $paymentAdhesionTransfer20->setAmount(20);
        $paymentAdhesionTransfer20->setType($paymentTypeCard);
        $paymentAdhesionTransfer20->setDateReceived(new \DateTime());
        $paymentAdhesionTransfer20->setDateCashed(new \DateTime());

        $manager->persist($paymentAdhesionTransfer20);

        // Generate several other payments
        $otherPayments = [];
        $i = 1;
        do
        {
            $paymentAdhesionCheque20 = new Payment();
            $paymentAdhesionCheque20->setAmount(20);
            $paymentAdhesionCheque20->setType($paymentTypeCheck);

            $manager->persist($paymentAdhesionCheque20);
            $otherPayments[] = $paymentAdhesionCheque20;

            $i++;
        }
        while ($i === 15);

        // Date managment
        $plusOneYear = new \DateInterval('P1Y');
        $removeOneYear = clone $plusOneYear;
        $removeOneYear->invert = 1;

        $now = new \DateTime();

        $inOneYear = clone $now;
        $inOneYear->add($plusOneYear);

        $lastYear = clone $now;
        $lastYear->add($removeOneYear);

        // Retreiving adherent.e.s from DB
        $peopleRepository = $manager->getRepository(People::class);
        $people = $peopleRepository->findWithNoMembership();
        $members = [];
        foreach ($people as $individual)
        {
            if (substr($individual->getUser()->getUsername(), 0, 4) === 'adhe')
            {
                $members[]['member'] = $individual;
            }
        }

        // -- Memberships -- //
        // First regular
        $membershipNormal1 = new Membership();

        $membershipNormal1
            ->setAmount(20)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionCheque50)
            ->setType($MembershipTypeRegular)
            ->setFiscalYear(2020);

        $manager->persist($membershipNormal1);

        // Second regular
        $membershipNormal2 = new Membership();

        $membershipNormal2
            ->setAmount(20)
            ->setDateStart($lastYear)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionCash20)
            ->setType($MembershipTypeRegular)
            ->setFiscalYear(2020);

        $manager->persist($membershipNormal2);

        // Family
        $membershipFamily = new Membership();

        $membershipFamily->setAmount(30);
        $membershipFamily->setDateStart($now);
        $membershipFamily->setDateEnd($inOneYear);
        $membershipFamily->setPayment($paymentAdhesionHelloAsso30);
        $membershipFamily->setType($MembershipTypeFamily);
        $membershipFamily->setFiscalYear(2020);

        $manager->persist($membershipFamily);

        // Adding memberships to all the members
        foreach ($members as $index => $member)
        {
            // regular membership
            $membershipRegular = new Membership();
            // Card payment generated for this membership
            $paymentAdhesionCard20 = new Payment();
            $paymentAdhesionCard20->setAmount(20);
            $paymentAdhesionCard20->setType($paymentTypeCard);
            $paymentAdhesionCard20->setDateReceived(new \DateTime());
            $paymentAdhesionCard20->setDateCashed(new \DateTime());

            // Create and associate active and inactive memberships evenly on members
            if ($index % 2 == 0)
            {
                // Active membership
                $membershipRegular
                    ->setAmount(20)
                    ->setDateStart($now)
                    ->setDateEnd($inOneYear)
                    ->setPayment($paymentAdhesionCard20)
                    ->setType($MembershipTypeRegular)
                    ->setFiscalYear(2020);
            }
            else
            {
                // Inactive membership
                $membershipRegular
                    ->setAmount(20)
                    ->setDateStart($lastYear)
                    ->setDateEnd($now)
                    ->setPayment($paymentAdhesionCard20)
                    ->setType($MembershipTypeRegular)
                    ->setFiscalYear(2020);
            }
            $manager->persist($membershipRegular);
            $members[$index]['membership'] = $membershipRegular;
        }

        // Adding payers to the firsts payments
        $membershipNormal2->getPayment()->setPayer($members[0]['member']);
        $membershipFamily->getPayment()->setPayer($members[1]['member']);
        $membershipNormal1->getPayment()->setPayer($members[2]['member']);
        $membershipFamily->getPayment()->setPayer($members[2]['member']);
        // Save the payments
        $manager->persist($membershipNormal2->getPayment());
        $manager->persist($membershipFamily->getPayment());
        $manager->persist($membershipNormal1->getPayment());
        $manager->persist($membershipFamily->getPayment());
        // Adding other memberships to the people
        $members[0]['member']->addMembership($membershipNormal2);
        $members[1]['member']->addMembership($membershipFamily);
        $members[2]['member']->addMembership($membershipNormal1);
        $members[2]['member']->addMembership($membershipFamily);
        // Save the memberships of the members
        $manager->persist($members[0]['member']);
        $manager->persist($members[1]['member']);
        $manager->persist($members[2]['member']);

        // Add a little more memberships to the people
        // using previously created memberships
        foreach ($members as $index => $memberAndMembership)
        {
            $member = $memberAndMembership['member'];
            $membership = $memberAndMembership['membership'];
            $payment = $membershipNormal2->getPayment();
            $member->addMembership($membership);
            $payment->setPayer($member);
            $manager->persist($payment);
            $manager->persist($member);
        }

        // Final flush
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
