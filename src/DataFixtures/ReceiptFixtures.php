<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Entity\Payment;
use App\Entity\Receipt;

class ReceiptFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct()
    {
    }

    public static function getGroups(): array
    {
        return ['receipts'];
    }

    public function load(ObjectManager $manager)
    {
        // Create logger used to display information messages
        $output = new ConsoleOutput();

        $paymentRepository = $manager->getRepository(Payment::class);

        $payments = $paymentRepository->findAll();

        // Order payments by their received date desc
        usort($payments, function($payment1, $payment2) {
            return $payment1->getDateReceived() > $payment2->getDateReceived();
        });

        // Sort payments by year
        $paymentsSortedByYear = [];
        foreach ($payments as $payment)
        {
            $year = $payment->getDateReceived()->format('Y');
            $paymentsSortedByYear[$year][] = $payment;
        }

        // Generate receipts using payments data
        foreach ($paymentsSortedByYear as $year => $payments)
        {
            $i = 1;
            foreach ($payments as $payment)
            {
                $receipt = new Receipt();
                $receipt->setPayment($payment);
                $receipt->setFiscalYear($year);
                $receipt->setOrderNumber($i++);
                $manager->persist($receipt);
            }
        }
        $manager->flush();
        $output->writeln('      <comment>></comment> <info>Receipts creation complete</info>');
    }

    public function getDependencies()
    {
        return array(
            MembershipFixtures::class,
            DonationFixtures::class,
        );
    }
}
