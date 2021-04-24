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
     * List all people who habe been members but not anymore and return
     * the list as a formatted array
     * @return json
     * @Route("/list/old-members", name="list_old_members", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getOldMembersListAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository(People::class)->findWithOutdatedMembership();

        $peopleData = [];
        foreach ($people as $individual) {
            $individualAddress = $individual->getAddresses()[0];
            $individualData = [
                'id' => $individual->getId(),
                'denomination' => $individual->getDenomination()->getLabel(),
                'firstname' => $individual->getFirstName(),
                'lastname' => $individual->getLastName(),
                'email_address' => $individual->getEmailAddress(),
                'home_phone_number' => $individual->getHomePhoneNumber(),
                'cell_phone_number' => $individual->getCellPhoneNumber(),
                'work_phone_number' => $individual->getWorkPhoneNumber(),
                'address' => [
                    'line' => $individualAddress->getLine(),
                    'line_two' => $individualAddress->getLineTwo(),
                    'postal_code' => $individualAddress->getPostalCode(),
                    'city' => $individualAddress->getCity(),
                    'country' => $individualAddress->getCountry(),
                ],
                'last_membership_year' => '2021',
            ];

            $peopleData[] = $individualData;
        }

        $response = new Response();
        $response->setContent(json_encode([
            'data' => $peopleData,
        ]));

        $response->headers->set('Content-Type', 'application/json');

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
