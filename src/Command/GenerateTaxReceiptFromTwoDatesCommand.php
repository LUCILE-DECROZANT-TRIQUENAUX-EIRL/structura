<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ReceiptService;
use App\Entity\Payment;

class GenerateTaxReceiptFromTwoDatesCommand extends Command
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var ReceiptService $receiptService
     */
    private $receiptService;

    /**
     * Class constructor with its dependecies injections.
     *
     * @param EntityManagerInterface $em The symfony entity manager. This parameter is a dependency injection.
     * @param ReceiptService $receiptService The service used to manage pdf fils. This parameter is a dependency injection.
     */
    public function __construct(
        EntityManagerInterface $em,
        ReceiptService $receiptService
    )
    {
        // Calling the parent constructor
        parent::__construct();

        $this->em = $em;
        $this->receiptService = $receiptService;

    }

    // Name of the command (the part after "bin/console")
    protected static $defaultName = 'app:generate:tax-receipt:fromtwodates';

    protected function configure()
    {
        // Short description shown while running "php bin/console list"
        $this->setDescription('Generate a tax receipt PDF.')

        // The full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command will generate a PDF file containing all tax receipt for which
        the payment has been made between the two given dates.')

        // Adding arguments
        ->addArgument(
            'from',
            InputArgument::REQUIRED,
            'Start date of the interval from which we want the tax receipts'
        )
        ->addArgument(
            'to',
            InputArgument::REQUIRED,
            'End date of the interval from which we want the tax receipts'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Job started');
        // Saving the script start time
        $start = new \DateTime();

        // Getting the user entries and making DateTime object from them
        $from = new \DateTime($input->getArgument('from') . ' 00:00:00');
        $to   = new \DateTime($input->getArgument('to') . ' 00:00:00');

        $output->writeln(\sprintf(
            'Finding all payments made between the %s and the %s...',
            $from->format('d/m/Y'),
            $to->format('d/m/Y')
        ));
        $payments = $this->em->getRepository(Payment::class)->findBetweenTwoDates($from, $to);

        $output->writeln('Generating the tax receipts...');
        $this->receiptService->generateTaxReceiptPdf($payments);

        // Saving the script end time
        $end = new \DateTime();

        // Calculating the execution time
        $executionTime = $start->diff($end);

        $output->writeln(\sprintf('File generation completed in %s', $executionTime->format('%H:%I:%S')));
    }
}