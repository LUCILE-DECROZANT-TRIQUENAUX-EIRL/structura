<?php

namespace App\Controller\Ajax;

use App\Entity\People;
use App\Form\GenerateTaxReceiptFromYearType;
use App\FormDataObject\GenerateTaxReceiptFromYearFDO;
use App\Service\ReceiptService;
use App\Entity\Receipt;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/ajax/people", name="ajax_people_")
 */
class PeopleAjaxController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/{id}",
     *     name = "show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPeopleAction(People $people)
    {
        return $this->handleView($this->view($people));
    }

    /**
     * Finds and displays the recap of a people.
     * @return views
     * @param People $people The user we want the recap
     * @Route("/{id}/recap", name="recap", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPeopleRecapAction(People $people)
    {
        $response = $this->render('Membership/_people-recap.html.twig', array(
            'people' => $people,
        ));

        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Finds and displays the placeholder for a people.
     * @return views
     * @Route("/placeholder", name="placeholder", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPeoplePlaceholderAction()
    {
        $response = $this->render('Membership/_people-placeholder.html.twig');

        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Generate and return the PDF containing all the receipts for a given year
     *
     * @param Request $request The request.
     * @param People $people The people for which we want the file
     * @Route("/generate/from-year/{id}", name="generate_from_year", methods={"POST"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function generateReceiptsByYearAction(Request $request, People $people, ReceiptService $receiptService)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $year = $request->request->all()['generate_tax_receipt_from_year']['year'];

        // Get the receipts needed in the file
        $receipts = $em->getRepository(Receipt::class)->findByYearAndPeople($year, $people);

        $filename = 'recus-fiscaux_' . $people->getFirstName() . '-' . $people->getLastName();

        // Generate the PDF and stream it
        $receiptService->generateTaxReceiptPdf(
            $receipts,
            $filename,
            new \DateTime(),
            true,
            true
        );

        return;
    }
}