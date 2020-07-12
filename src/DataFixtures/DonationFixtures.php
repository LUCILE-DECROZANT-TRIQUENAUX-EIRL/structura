<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Entity\Bank;
use App\Entity\Donation;
use App\Entity\Membership;
use App\Entity\MembershipType;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\People;

class DonationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct()
    {
    }

    public static function getGroups(): array
    {
        return ['donations'];
    }

    public function load(ObjectManager $manager)
    {
        // Create logger used to display information messages
        $output = new ConsoleOutput();

        // Get the payments bigger than the membership type they were paying for
        $output->writeln('      <comment>></comment> <info>Donations linked to membership payments creation...</info>');
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);

        $bankRepository = $manager->getRepository(Bank::class);
        $banks = $bankRepository->findAll();
        $banksCount = count($banks);

        $paymentsTooBig = $paymentTypeRepository->findByAmountTooBigForMembership();
        foreach ($paymentsTooBig as $payment)
        {
            $membershipType = $payment->getMembership()->getType();
            $membershipTypeAmount = $membershipType->getDefaultAmount();
            $donationAmount = $payment->getAmount();

            $donation = new Donation();
            $donation->setAmount((float) $payment->getAmount() - $membershipTypeAmount);
            $donation->setDonator($payment->getPayer());
            $donation->setPayment($payment);
            $donation->setDonationDate($payment->getDateCashed());
            $manager->persist($donation);
        }

        // Generate several other donations
        $output->writeln('      <comment>></comment> <info>Other donations creation...</info>');
        $peopleRepository = $manager->getRepository(People::class);
        $people = $peopleRepository->findAll();
        $paymentTypeCheck = $paymentTypeRepository->findOneBy(['label' => 'Chèque']);

        foreach ($people as $index => $individual)
        {
            if ($index % 3 == 0)
            {
                $amountDonation = (float) rand (20, 1000);
                $dateDonation = rand (2017, 2019) . '-02-15 ' . rand (10, 23) . ':00:00';

                $paymentDonationCheque = new Payment();
                $paymentDonationCheque->setAmount($amountDonation);
                $paymentDonationCheque->setType($paymentTypeCheck);
                $paymentDonationCheque->setDateReceived(new \DateTime($dateDonation));
                $paymentDonationCheque->setPayer($individual);

                $bank = $banks[rand(0, $banksCount - 1)];
                $paymentDonationCheque->setBank($bank);

                $manager->persist($paymentDonationCheque);

                $donation = new Donation();
                $donation->setAmount($amountDonation);
                $donation->setDonationDate(new \DateTime($dateDonation));
                $donation->setDonator($individual);
                $donation->setPayment($paymentDonationCheque);
                $manager->persist($donation);
            }
        }
        $manager->flush();
        $output->writeln('      <comment>></comment> <info>Donations creation complete</info>');
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            MembershipFixtures::class,
            BankFixtures::class,
        );
    }
}
