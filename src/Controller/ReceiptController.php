<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Payment;

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
        return $this->render('Receipt/list.html.twig', [
            'generatedAnnualReceipts' => null,
            'generatedBetweenTwoDatesReceipts' => null,
            'generatedParticularReceipts' => null,
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

}
