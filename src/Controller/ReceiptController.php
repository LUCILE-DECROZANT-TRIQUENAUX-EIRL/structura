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
use Symfony\Component\HttpFoundation\Response;
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
        ReceiptService $receiptService,
        TranslatorInterface $translator
    )
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Find fiscal years for which there is receipts to generate
        $availableFiscalYears = $em->getRepository(Receipt::class)->findAvailableFiscalYears();

        // Setup options for each fiscal year
        $availableFiscalYearsData = [];
        foreach ($availableFiscalYears as $availableFiscalYear)
        {
            // Check if a file is currently generated
            $filesBeingCurrentlyGenerated = $em->getRepository(ReceiptsFromFiscalYearGroupingFile::class)
                    ->findByGenerationInProgress($availableFiscalYear);
            if (count($filesBeingCurrentlyGenerated) > 0)
            {
                $dataIsUnderGeneration = [
                    'data-is-under-generation' => 'true',
                ];
            }
            else
            {
                $dataIsUnderGeneration = [
                    'data-is-under-generation' => 'false',
                ];
            }

            // Get the last generation date if it exists
            $lastFileGenerated = $em->getRepository(ReceiptsFromFiscalYearGroupingFile::class)
                    ->findLastGenerated($availableFiscalYear);
            if (!empty($lastFileGenerated))
            {
                $dataLastGenerationDate = [
                    'data-last-generation-date' => $lastFileGenerated->getReceiptsGenerationBase()->getGenerationDateEnd()->format('Y-m-d\TH:i:s'),
                ];
            }
            else
            {
                $dataLastGenerationDate = [
                    'data-last-generation-date' => 'false',
                ];
            }
            $availableFiscalYearsData[$availableFiscalYear] = array_merge(
                $dataIsUnderGeneration,
                $dataLastGenerationDate
            );
        }

        // Creating an empty FDO
        $generateTaxReceiptFromFiscalYearFDO = new GenerateTaxReceiptFromFiscalYearFDO();

        // From creation
        $generateFromFiscalYearForm = $this->createForm(
            GenerateTaxReceiptFromFiscalYearType::class,
            $generateTaxReceiptFromFiscalYearFDO,
            [
                'availableFiscalYears' => $availableFiscalYears,
                'availableFiscalYearsData' => $availableFiscalYearsData,
            ]
        );

        $generateFromFiscalYearForm->handleRequest($request);

        // Submit
        if ($generateFromFiscalYearForm->isSubmitted() && $generateFromFiscalYearForm->isValid())
        {
            $fiscalYear = $generateTaxReceiptFromFiscalYearFDO->getFiscalYear();

            // Creating the database log
            $receiptGenerationDate = new \DateTime();
            $receiptsGroupingFile = new ReceiptsGroupingFile();
            $receiptsGroupingFile->setGenerationDateStart($receiptGenerationDate);
            $receiptsGroupingFile->setGenerator($this->getUser());
            $receiptsFromFiscalYearGroupingFile = new ReceiptsFromFiscalYearGroupingFile();
            $receiptsFromFiscalYearGroupingFile->setFiscalYear($fiscalYear);
            $receiptsFromFiscalYearGroupingFile->setReceiptsGenerationBase($receiptsGroupingFile);

            // Save that the file is being generated
            $em->persist($receiptsGroupingFile);
            $em->persist($receiptsFromFiscalYearGroupingFile);
            $em->flush();

            $messageBus->dispatch(
                    new GenerateReceiptFromFiscalYearMessage(
                            $receiptsFromFiscalYearGroupingFile->getId(),
                            $this->getUser()->getId()
            ));

            $this->addFlash(
                    'success', $translator->trans('Génération du PDF en cours...')
            );

            return $this->redirectToRoute('receipt_list');
        }

        return $this->render('Receipt/generate-from-fiscal-year.html.twig', [
            'from_fiscal_year_form' => $generateFromFiscalYearForm->createView(),
        ]);
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

    /**
     * @param ReceiptsGroupingFile $file
     * @return Response
     * @Route("/check-generation-grouped-pdf/{id}", name="check_generation_grouped_receipts_pdf", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function checkGenerationGroupedPdfAction(ReceiptsGroupingFile $file)
    {
        $response = new Response();
        $response->setContent(json_encode([
            'isGenerationComplete' => !empty($file->getGenerationDateEnd()),
            'downloadUrl' => $this->generateUrl('download_grouped_receipts_pdf', ['id' => $file->getId()]),
        ]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
