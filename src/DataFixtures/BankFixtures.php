<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Entity\Bank;

class BankFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct()
    {
    }

    public static function getGroups(): array
    {
        return ['banks'];
    }

    public function load(ObjectManager $manager)
    {
        // Create logger used to display information messages
        $output = new ConsoleOutput();

        // Create pool of banks
        $output->writeln('      <comment>></comment> <info>Banks creation...</info>');
        $bankRepository = $manager->getRepository(Bank::class);

        $bank = new Bank();
        $bank->setName('Société Générale');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('BNP Paribas');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('La Banque Postale');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('HSBC');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('LCL');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('Allianz Banque');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('AXA Banque');
        $manager->persist($bank);
        $bank = new Bank();
        $bank->setName('Groupama Banque');
        $manager->persist($bank);

        $manager->flush();
        $output->writeln('      <comment>></comment> <info>Banks creation complete</info>');
    }
}
