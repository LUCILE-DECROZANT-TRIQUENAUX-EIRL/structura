<?php

namespace App\Service;

use Twig\Environment;
use App\Entity\Payment;
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
     * Class constructor with its dependecies injections
     *
     * @param Environment $twig The templating engine used to process twig. This parameter is a dependency injection.
     * @param FileService $fileService Utility service to manipulate files. This parameter is a dependency injection.
     */
    public function __construct(
        Environment $twig,
        FileService $fileService
    )
    {
        $this->twig = $twig;
        $this->fileService = $fileService;
    }

    /**
     * Generate a single pdf file containing all the receipts corresponding to a list of given payments.
     *
     * @param array $payments An array containing all the payments for which a receipt need to be generated.
     * @param string $filename The name of the newly generated file (without the path or the extension).
     */
    public function generateTaxReceiptPdf(
        array $payments,
        string $filename = 'tax_receipts'
    )
    {
        // Generation date for historisation
        $receiptGenerationDate = new \DateTime('now');
        // File fullname, which includes the directory and the file's extension
        $fileFullName = 'pdf/'. $receiptGenerationDate->format('Y-m-d:H-i-s') . '_' . $filename . '.pdf';

        // We render the twig template of a tax receipt into pure html
        $htmlNeedingConversion = $this->twig->render('PDF/Receipt/_tax_receipt_base.html.twig', [
            'payments' => $payments,
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
        $this->fileService->file_force_contents($fileFullName, $output);
    }
}