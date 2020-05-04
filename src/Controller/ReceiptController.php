<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
}
