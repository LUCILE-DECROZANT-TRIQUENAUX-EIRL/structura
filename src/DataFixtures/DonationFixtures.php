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
use App\Entity\Donation;

class DonationFixtures extends Fixture implements FixtureGroupInterface
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
        // Get the payments bigger than the membership type they were paying for
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);

        $paymentsTooBig = $paymentTypeRepository->findByAmountTooBigForMembership();
        foreach ($paymentsTooBig as $payment)
        {
            $membershipType = $payment->getMembership()->getType();
            $membershipTypeAmount = $membershipType->getDefaultAmount();
            $donationAmount = $payment->getAmount();

            $donation = new Donation();
            $donation->setAmount((float) $membershipTypeAmount - $donationAmount);
            $donation->setDonator($payment->getPayer());
        }

        // Generate several other donations
        $peopleRepository = $manager->getRepository(People::class);
        $people = $peopleRepository->findAll();
        $paymentTypeCheck = $paymentTypeRepository->findOneBy(['label' => 'Chèque']);

        foreach ($people as $index => $individual)
        {
            if ($index % 3 == 0)
            {
                $amountDonation = (float) rand (20, 1000);

                $paymentDonationCheque = new Payment();
                $paymentDonationCheque->setAmount($amountDonation);
                $paymentDonationCheque->setType($paymentTypeCheck);
                $paymentDonationCheque->setPayer($individual);
                $manager->persist($paymentDonationCheque);

                $donation = new Donation();
                $donation->setAmount($amountDonation);
                $donation->setDonationDate(new \DateTime());
                $donation->setDonator($individual);
                $donation->setPayment($paymentDonationCheque);
                $manager->persist($donation);
            }
        }
        $manager->flush();
    }
}
