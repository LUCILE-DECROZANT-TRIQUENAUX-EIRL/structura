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
/**
 * @Route(path="/{_locale}/receipt", requirements={"_locale"="en|fr"})
 */
class ReceiptController extends AbstractController
{
    /**
     * @return views
     * @param Request $request The request.
     * @Route("/", name="receipt_dashboard", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function dashboardAction(Request $request)
    {
        return $this->render('Receipt/dashboard.html.twig', []);
    }

    /**
     * @return void
     * @param Request $request The request.
     * @Route("/pdf", name="pdf", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function pdfAction(Request $request)
    {
        $html = $this->renderView('PDF/Receipt/_tax_receipt.html.twig');

        // Object instantiation
        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);

        // Options that set the page format and orientation
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream('tax_receipt.pdf', [
            'Attachment' => false // If set to true, force download, else give a preview
        ]);

        // It bugs if the process is not killed
        // It's not a big deal since this controller will be updated and redesigned
        die;
    }
}
