<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Payment;
use App\Entity\Receipt;
use App\Entity\ReceiptsGroupingFile;
use App\Entity\ReceiptsFromFiscalYearGroupingFile;
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
     * Class constructor with its dependecies injections
     *
     * @param Environment $twig The templating engine used to process twig. This parameter is a dependency injection.
     * @param FileService $fileService Utility service to manipulate files. This parameter is a dependency injection.
     */
    public function __construct(
        Environment $twig,
        FileService $fileService,
        EntityManagerInterface $em
    )
    {
        $this->twig = $twig;
        $this->fileService = $fileService;
        $this->em = $em;
    }

    /**
     * Generate a single pdf file containing all the receipts corresponding to a list of given payments.
     *
     * @param array $receipts An array containing all the receipts that need to be generated.
     * @param string $filename The name of the newly generated file (without the path or the extension).
     * @param \Datetime $receiptGenerationDate The datetime when the generation started.
     */
    public function generateTaxReceiptPdf(
        User $user,
        array $receipts,
        string $filename = 'tax_receipts',
        \DateTime $receiptGenerationDate
    )
    {
        $receiptsGroupingFile = new ReceiptsGroupingFile();
        $receiptsGroupingFile->setGenerationDateStart($receiptGenerationDate);
        $receiptsGroupingFile->setGenerator($user);
        $receiptsGroupingFile->setReceipts($receipts);

        // File fullname, which includes the file's extension
        $fullFilename = $receiptGenerationDate->format('Y-m-d:H-i-s') . '_' . $filename . '.pdf';
        $receiptsGroupingFile->setFilename($fullFilename);

        // We render the twig template of a tax receipt into pure html
        $htmlNeedingConversion = $this->twig->render('PDF/Receipt/_tax_receipt_base.html.twig', [
            'receipts' => $receipts,
            'receiptGenerationDate' => $receiptGenerationDate,
        ]);

        // PDF generator object instantiation
        $dompdf = new Dompdf();

        // Loading previously rendered html
        $dompdf->loadHtml($htmlNeedingConversion);

        // Set the page format and orientation
        $dompdf->setPaper('A4', 'portrait');

        // PDF rendering
        $dompdf->render();

        // Saving the rendering's output
        $output = $dompdf->output();

        // Saving the generated file on the server
        $fileLocation = '../pdf/' . $fullFilename;
        $this->fileService->file_force_contents($fileLocation, $output);

        $receiptsGroupingFile->setGenerationDateEnd(new \DateTime());
        $this->em->persist($receiptsGroupingFile);
        $this->em->flush();

        return $receiptsGroupingFile;
    }

    public function generateTaxReceiptPdfFromFiscalYear($fiscalYear, $user)
    {
        $receiptGenerationDate = new \DateTime();
        $receiptsFromFiscalYearGroupingFile = new ReceiptsFromFiscalYearGroupingFile();
        $receiptsFromFiscalYearGroupingFile->setFiscalYear($fiscalYear);

        $receipts = $this->em->getRepository(Receipt::class)->findByFiscalYear($fiscalYear);

        $receiptsGroupingFile = $this->generateTaxReceiptPdf(
            $user,
            $receipts,
            'recus-fiscaux_'.$fiscalYear,
            $receiptGenerationDate
        );

        $receiptsFromFiscalYearGroupingFile->setReceiptsGenerationBase($receiptsGroupingFile);
        $this->em->persist($receiptsFromFiscalYearGroupingFile);
        $this->em->flush();
    }
}