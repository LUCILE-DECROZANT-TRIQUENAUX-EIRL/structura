<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Bank;
use App\Entity\Membership;
use App\Entity\MembershipType;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\People;

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
        // Create logger used to display information messages
        $output = new ConsoleOutput();

        // Creating MembershipType
        $output->writeln('      <comment>></comment> <info>Membership types creation...</info>');
        $bankRepository = $manager->getRepository(Bank::class);
        $banks = $bankRepository->findAll();
        $banksCount = count($banks);

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

        $output->writeln('      <comment>></comment> <info>Payments creation...</info>');
        // First payment (check)
        $paymentAdhesionCheque50 = new Payment();
        $paymentAdhesionCheque50->setAmount(50);
        $paymentAdhesionCheque50->setType($paymentTypeCheck);
        $paymentAdhesionCheque50->setDateReceived(new \DateTime('2017-02-17 16:01:57'));
        $paymentAdhesionCheque50->setDateCashed(new \DateTime('2017-02-17 16:01:57'));
        $bank = $banks[rand(0, $banksCount - 1)];
        $paymentAdhesionCheque50->setBank($bank);
        $paymentAdhesionCheque50->setCheckNumber('000111222333');

        $manager->persist($paymentAdhesionCheque50);

        // Second payment (hello asso)
        $paymentAdhesionHelloAsso30 = new Payment();
        $paymentAdhesionHelloAsso30->setAmount(30);
        $paymentAdhesionHelloAsso30->setType($paymentTypeHelloAsso);
        $paymentAdhesionHelloAsso30->setDateReceived(new \DateTime('2017-01-18 14:36:03'));
        $paymentAdhesionHelloAsso30->setDateCashed(new \DateTime('2017-01-18 14:36:03'));

        $manager->persist($paymentAdhesionHelloAsso30);

        // Third payment (cash)
        $paymentAdhesionCash20 = new Payment();
        $paymentAdhesionCash20->setAmount(20);
        $paymentAdhesionCash20->setType($paymentTypeCash);
        $paymentAdhesionCash20->setDateReceived(new \DateTime('2019-10-27 12:21:09'));
        $paymentAdhesionCash20->setDateCashed(new \DateTime('2019-10-27 12:21:09'));

        $manager->persist($paymentAdhesionCash20);

        // Fourth payment (card)
        // Not persisting this one, it will be used later as template tho
        $paymentAdhesionCard20 = new Payment();
        $paymentAdhesionCard20->setAmount(20);
        $paymentAdhesionCard20->setType($paymentTypeCard);
        $paymentAdhesionCard20->setDateReceived(new \DateTime('2018-05-11 08:28:09'));
        $paymentAdhesionCard20->setDateCashed(new \DateTime('2018-05-11 08:28:09'));


        // Fifth payment (transfer)
        $paymentAdhesionTransfer20 = new Payment();
        $paymentAdhesionTransfer20->setAmount(20);
        $paymentAdhesionTransfer20->setType($paymentTypeTransfer);
        $paymentAdhesionTransfer20->setDateReceived(new \DateTime('2018-04-25 10:59:02'));
        $paymentAdhesionTransfer20->setDateCashed(new \DateTime('2018-04-25 10:59:02'));

        $manager->persist($paymentAdhesionTransfer20);

        // Date managment
        $plusOneYear = new \DateInterval('P1Y');
        $removeOneYear = clone $plusOneYear;
        $removeOneYear->invert = 1;

        $now = new \DateTime();
        $thisYear = date('Y', $now->getTimestamp());

        $inOneYear = clone $now;
        $inOneYear->add($plusOneYear);

        $oneYearAgo = clone $now;
        $oneYearAgo->add($removeOneYear);
        $lastYear = date('Y', $oneYearAgo->getTimestamp());

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

        $nbMembers = count($members);

        // -- Memberships -- //
        $output->writeln('      <comment>></comment> <info>Memberships creation...</info>');
        // First regular
        $membershipNormal1 = new Membership();

        $membershipNormal1
            ->setAmount(20)
            ->setDateStart($oneYearAgo)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionCheque50)
            ->setType($MembershipTypeRegular)
            ->setFiscalYear($lastYear);

        $manager->persist($membershipNormal1);

        // Second regular
        $membershipNormal2 = new Membership();

        $membershipNormal2
            ->setAmount(20)
            ->setDateStart($now)
            ->setDateEnd($inOneYear)
            ->setPayment($paymentAdhesionCash20)
            ->setType($MembershipTypeRegular)
            ->setFiscalYear($thisYear);

        $manager->persist($membershipNormal2);

        // Second regular
        $membershipNormal3 = new Membership();

        $membershipNormal3
            ->setAmount(20)
            ->setDateStart($oneYearAgo)
            ->setDateEnd($now)
            ->setPayment($paymentAdhesionTransfer20)
            ->setType($MembershipTypeRegular)
            ->setFiscalYear($lastYear);

        $manager->persist($membershipNormal3);

        // Family
        $membershipFamily = new Membership();

        $membershipFamily->setAmount(30);
        $membershipFamily->setDateStart($now);
        $membershipFamily->setDateEnd($inOneYear);
        $membershipFamily->setPayment($paymentAdhesionHelloAsso30);
        $membershipFamily->setType($MembershipTypeFamily);
        $membershipFamily->setFiscalYear($thisYear);

        $manager->persist($membershipFamily);

        // Adding payers to the firsts payments
        $membershipFamily->getPayment()->setPayer($members[2]['member']);
        $membershipNormal1->getPayment()->setPayer($members[2]['member']);
        $membershipNormal2->getPayment()->setPayer($members[0]['member']);
        $membershipNormal3->getPayment()->setPayer($members[1]['member']);

        // Save the payments
        $manager->persist($membershipFamily->getPayment());
        $manager->persist($membershipNormal1->getPayment());
        $manager->persist($membershipNormal2->getPayment());
        $manager->persist($membershipNormal3->getPayment());

        // Adding memberships to the first members
        $members[2]['member']->addMembership($membershipFamily);
        $members[1]['member']->addMembership($membershipFamily);
        $members[2]['member']->addMembership($membershipNormal1);
        $members[0]['member']->addMembership($membershipNormal2);
        $members[1]['member']->addMembership($membershipNormal3);

        // Save the memberships of the members
        $manager->persist($members[0]['member']);
        $manager->persist($members[1]['member']);
        $manager->persist($members[2]['member']);

        // Adding memberships to all the members except the one we already added memberships
        for ($index = 3; $index < $nbMembers; $index++)
        {
            // regular membership
            $membershipRegular = new Membership();

            // Card payment generated for this membership
            $payment = clone $paymentAdhesionCard20;
            // Change date received and date cashed to add more diversity and cohesion
            $days = rand(1, 200);
            $paymentDateTimestamp = strtotime('-' . $days . ' days', $now->getTimestamp());
            $paymentDate = new \DateTime('@' . $paymentDateTimestamp);
            $payment->setDateReceived($paymentDate);
            $payment->setDateCashed($paymentDate);

            // Create and associate active and inactive memberships evenly on members
            if ($index % 2 == 0)
            {
                // Active membership
                $membershipRegular
                    ->setAmount(20)
                    ->setDateStart($now)
                    ->setDateEnd($inOneYear)
                    ->setPayment($payment)
                    ->setType($MembershipTypeRegular)
                    ->setFiscalYear($thisYear);
            }
            else
            {
                // Inactive membership
                $membershipRegular
                    ->setAmount(20)
                    ->setDateStart($oneYearAgo)
                    ->setDateEnd($now)
                    ->setPayment($payment)
                    ->setType($MembershipTypeRegular)
                    ->setFiscalYear($lastYear);
            }
            $manager->persist($membershipRegular);
            $manager->persist($payment);
            $members[$index]['membership'] = $membershipRegular;
        }

        // Add a little more memberships to the people
        // using previously created memberships
        for ($index = 3; $index < $nbMembers; $index++)
        {
            $member = $members[$index]['member'];
            $membership = $members[$index]['membership'];
            $payment = $membership->getPayment();
            $member->addMembership($membership);
            $payment->setPayer($member);
            $manager->persist($payment);
            $manager->persist($member);
        }

        // Final flush
        $manager->flush();
        $output->writeln('      <comment>></comment> <info>Memberships creation complete</info>');
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
