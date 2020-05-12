<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\Messenger\MessageBusInterface;
use Dompdf\Dompdf;
use App\Service\ReceiptService;
use App\Entity\Payment;
use App\Entity\Receipt;
use App\Entity\ReceiptsGroupingFile;
use App\Entity\ReceiptsFromFiscalYearGroupingFile;
use App\Message\GenerateReceiptFromFiscalYearMessage;
use App\FormDataObject\GenerateTaxReceiptFromFiscalYearFDO;
use App\Form\GenerateTaxReceiptFromFiscalYearType;

/**
 * @Route(path="/{_locale}/receipt", requirements={"_locale"="en|fr"})
 */
class ReceiptController extends AbstractController
{
    /**
     * @return views
     * @param Request $request The request.
     * @Route("/", name="receipt_list", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $annualReceiptsFiles = $em->getRepository(ReceiptsFromFiscalYearGroupingFile::class)
                ->findAll();

        return $this->render('Receipt/list.html.twig', [
            'generatedAnnualReceipts' => $annualReceiptsFiles,
            'generatedBetweenTwoDatesReceipts' => null,
            'generatedParticularReceipts' => null,
        ]);
    }

    /**
     * @return views
     * @param Request $request The request.
     * @Route("/generate/from-fiscal-year", name="receipt_generate_from_fiscal_year", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function generateFromFiscalYearAction(
        Request $request,
        MessageBusInterface $messageBus,
        ReceiptService $receiptService
    )
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $availableFiscalYears = $em->getRepository(Receipt::class)->findAvailableFiscalYears();

        // Creating an empty FDO
        $generateTaxReceiptFromFiscalYearFDO = new GenerateTaxReceiptFromFiscalYearFDO();

        // From creation
        $generateFromFiscalYearForm = $this->createForm(
            GenerateTaxReceiptFromFiscalYearType::class,
            $generateTaxReceiptFromFiscalYearFDO,
            [
                'availableFiscalYears' => $availableFiscalYears
            ]
        );

        $generateFromFiscalYearForm->handleRequest($request);

        // Submit
        if ($generateFromFiscalYearForm->isSubmitted() && $generateFromFiscalYearForm->isValid())
        {
            $fiscalYear = $generateTaxReceiptFromFiscalYearFDO->getFiscalYear();

            $messageBus->dispatch(new GenerateReceiptFromFiscalYearMessage($fiscalYear, $this->getUser()->getId()));

            return $this->redirectToRoute('receipt_list');
        }

        return $this->render('Receipt/generate-from-fiscal-year.html.twig', [
            'from_fiscal_year_form' => $generateFromFiscalYearForm->createView(),
        ]);
    }

    /**
     * @return void
     * @param Request $request The request.
     * @Route("/pdf", name="pdf", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function pdfAction(Request $request)
    {
        // for testing purpose, get one payment from the database
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository(Payment::class)
                ->findOneById(1);

        // Render the PDF content using a twig template
        $htmlNeedingConversion = $this->renderView('PDF/Receipt/_tax_receipt.html.twig', [
            'orderCode' => '2014_812',
            'payment' => $payment,
            'receiptGenerationDate' => new \DateTime('2014-06-20 00:00:00'),
        ]);

        // Object instantiation
        $dompdf = new Dompdf();

        $dompdf->loadHtml($htmlNeedingConversion);

        // Set the page format and orientation
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream('tax_receipt.pdf', [
            'Attachment' => false // If set to true, force download, else give a preview
        ]);

        return true;
    }

    /**
     * @return BinaryFileResponse
     * @param Request $request The request.
     * @Route("/download-grouped-pdf/{id}", name="download_grouped_receipts_pdf", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function downloadGroupedPdfAction(ReceiptsGroupingFile $file)
    {
        $pdfFolderPath = $this->getParameter('kernel.project_dir') . '/pdf/';
        $filename = $file->getFilename();

        $response = new BinaryFileResponse($pdfFolderPath . $filename);

        // Set mime type of the file to pdf
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        $response->headers->set('Content-Type', 'application/pdf');

        // Will make the browser directly download and not try to open the pdf
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
