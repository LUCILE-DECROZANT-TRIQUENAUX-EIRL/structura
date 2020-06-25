<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Association;
use App\Entity\User;
use App\Entity\Payment;
use App\Entity\Receipt;
use App\Entity\ReceiptsGroupingFile;
use App\Entity\ReceiptsFromYearGroupingFile;
use App\Entity\ReceiptsFromTwoDatesGroupingFile;
use App\Service\Utils\FileService;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Class containing methods used to handle receipts.
 */
class ReceiptService
{
    /**
     * @var Environment $twig
     */
    private $twig;

    /**
     * @var FileService $fileService
     */
    private $fileService;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var ParameterBagInterface $params
     */
    private $projectDir;

    /**
     * Class constructor with its dependecies injections
     *
     * @param Environment $twig The templating engine used to process twig. This parameter is a dependency injection.
     * @param FileService $fileService Utility service to manipulate files. This parameter is a dependency injection.
     */
    public function __construct(
        Environment $twig,
        FileService $fileService,
        EntityManagerInterface $em,
        string $projectDir
    )
    {
        $this->twig = $twig;
        $this->fileService = $fileService;
        $this->em = $em;
        $this->projectDir = $projectDir;
    }

    /**
     * Generate a single pdf file containing all the receipts corresponding to a list of given payments.
     *
     * @param array $receipts An array containing all the receipts that need to be generated.
     * @param string $filename The name of the newly generated file (without the path or the extension).
     * @param \Datetime $receiptGenerationDate The datetime when the generation started.
     * @return string
     */
    public function generateTaxReceiptPdf(
        array $receipts,
        string $filename = 'tax_receipts',
        \DateTime $receiptGenerationDate,
        bool $isStreamed = false,
        bool $isFromController = false
    )
    {
        // Retreive association information
        $association = $this->em->getRepository(Association::class)->findOneById(1);

        // File fullname, which includes the file's extension
        $fullFilename = $receiptGenerationDate->format('Y-m-d:H-i-s') . '_' . $filename . '.pdf';

        // We render the twig template of a tax receipt into pure html
        $htmlNeedingConversion = $this->twig->render('PDF/Receipt/_tax_receipt_base.html.twig', [
            'association' => $association,
            'receipts' => $receipts,
            'receiptGenerationDate' => $receiptGenerationDate,
            'isFromController' => $isFromController,
        ]);

        // PDF generator object instantiation
        $dompdf = new Dompdf();

        // Loading previously rendered html
        $dompdf->loadHtml($htmlNeedingConversion);

        // Set the page format and orientation
        $dompdf->setPaper('A4', 'portrait');

        // PDF rendering
        $dompdf->render();

        if ($isStreamed)
        {
            $dompdf->stream($fullFilename, [
                'Attachment' => true // force download
            ]);
        }
        else
        {
            // Saving the rendering's output
            $output = $dompdf->output();

            // Saving the generated file on the server
            $fileLocation = $this->projectDir . '/pdf/' . $fullFilename;
            $this->fileService->file_force_contents($fileLocation, $output);

            return $fullFilename;
        }
    }

    public function generateTaxReceiptPdfFromYear($receiptsGroupingFileId, $userId)
    {
        // Get the user asking for the generation
        $user = $this->em->getRepository(User::class)->find($userId);

        // Get the receipts grouping file corresponding to this generation
        $receiptsFromYearGroupingFile = $this->em->getRepository(ReceiptsFromYearGroupingFile::class)->find($receiptsGroupingFileId);
        $receiptsGroupingFile = $receiptsFromYearGroupingFile->getReceiptsGenerationBase();
        $year = $receiptsFromYearGroupingFile->getYear();

        // Get the receipts needed in the file
        $receipts = $this->em->getRepository(Receipt::class)->findByYear($year);

        $fullFilename = $this->generateTaxReceiptPdf(
            $receipts,
            'recus-fiscaux_' . $year,
            $receiptsGroupingFile->getGenerationDateStart()
        );


        // Update the receipts grouping file
        $receiptsGroupingFile->setReceipts($receipts);
        $receiptsGroupingFile->setGenerationDateEnd(new \DateTime());
        $receiptsGroupingFile->setFilename($fullFilename);

        $this->em->persist($receiptsGroupingFile);
        $this->em->persist($receiptsFromYearGroupingFile);
        $this->em->flush();
    }

    public function generateTaxReceiptPdfFromTwoDates($receiptsGroupingFileId, $userId)
    {
        // Get the receipts grouping file corresponding to this generation
        $receiptsFromTwoDatesGroupingFile = $this->em->getRepository(ReceiptsFromTwoDatesGroupingFile::class)->find($receiptsGroupingFileId);
        $receiptsGroupingFile = $receiptsFromTwoDatesGroupingFile->getReceiptsGenerationBase();
        $fromDate = $receiptsFromTwoDatesGroupingFile->getDateFrom();
        $toDate = $receiptsFromTwoDatesGroupingFile->getDateTo();

        // Get the receipts needed in the file
        $receipts = $this->em->getRepository(Receipt::class)->findBetweenTwoDates($fromDate, $toDate);

        $fullFilename = $this->generateTaxReceiptPdf(
            $receipts,
            'recus-fiscaux_du-' . $fromDate->format('Ymd') . '-au-' . $toDate->format('Ymd'),
            $receiptsGroupingFile->getGenerationDateStart()
        );

        // Update the receipts grouping file
        $receiptsGroupingFile->setReceipts($receipts);
        $receiptsGroupingFile->setGenerationDateEnd(new \DateTime());
        $receiptsGroupingFile->setFilename($fullFilename);

        $this->em->persist($receiptsGroupingFile);
        $this->em->persist($receiptsFromTwoDatesGroupingFile);
        $this->em->flush();
    }
}