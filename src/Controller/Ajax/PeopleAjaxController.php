<?php

namespace App\Controller\Ajax;

use App\Entity\Address;
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
     * List all people who have been members but not anymore and return
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
             if (count($individual->getAddresses()) > 0) {
                $individualAddress = $individual->getAddresses()[0];
            } else {
                $individualAddress = new Address();
            }
            // Sort membership years
            $membershipYears = [];
            foreach ($individual->getMemberships() as $individualMembership) {
                $membershipYears[] = $individualMembership->getDateEnd()->format('Y');
            }
            rsort($membershipYears);

            // Format data
            $individualData = [
                'id' => $individual->getId(),
                'denomination' => $individual->getDenomination()->getLabel(),
                'firstname' => $individual->getFirstName(),
                'lastname' => mb_strtoupper($individual->getLastName(), 'UTF-8'),
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
                'last_membership_year' => $membershipYears[0],
                'show_individual_url' => $this->generateUrl('people_show', ['id' => $individual->getId()]),
                'add_membership_individual_url' => $this->generateUrl(
                    'membership_create',
                    ['person-id' => $individual->getId()]
                ),
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
     * List all memberships for a given people as a formatted array
     * @return json
     * @Route("/{id}/list/memberships", name="list_memberships", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getMembershipsListAction(People $people)
    {
        $membershipsData = [];
        foreach ($people->getMemberships() as $membership) {
            $formattedMembers = null;
            foreach ($membership->getMembers() as $member) {
                $formattedMember = [
                    'id' => $member->getId(),
                    'denomination' => $member->getDenomination()->getLabel(),
                    'firstname' => $member->getFirstName(),
                    'lastname' => $member->getLastName(),
                ];

                $formattedMembers[] = $formattedMember;
            }

            $isCurrent = false;
            if (!empty($people->getActiveMembership())) {
                $isCurrent = $membership->getId() === $people->getActiveMembership()->getId();
            }
            $payment = $membership->getPayment();
            $formattedMembership = [
                'id' => $membership->getId(),
                'type_label' => $membership->getType()->getLabel(),
                'type_description' => $membership->getType()->getDescription(),
                'price' => $membership->getType()->getDefaultAmount(),
                'date_start' => $membership->getDateStart()?->format('d/m/Y'),
                'date_end' => $membership->getDateEnd()?->format('d/m/Y'),
                'payment' => [
                    'amount' => $payment->getAmount(),
                    'mean' => $payment->getType()->getLabel(),
                    'date_received' => $payment->getDateReceived()?->format('d/m/Y'),
                    'date_cashed' => $payment->getDateCashed()?->format('d/m/Y'),
                    'payer' => [
                        'id' => $payment->getPayer()->getId(),
                        'denomination' => $payment->getPayer()->getDenomination()->getLabel(),
                        'firstname' => $payment->getPayer()->getFirstName(),
                        'lastname' => $payment->getPayer()->getLastName(),
                    ],
                    'fiscal_receipt' => [
                        'fiscal_year' => $payment->getReceipt()->getYear(),
                        'order_code' => $payment->getReceipt()->getOrderCode(),
                    ],
                    'comment' => $payment->getComment(),
                ],
                'comment' => $membership->getComment(),
                'members' => $formattedMembers,
                'is_current' => $isCurrent,
            ];

            $membershipsData[] = $formattedMembership;
        }

        $response = new Response();
        $response->setContent(json_encode([
            'data' => $membershipsData,
        ]));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * List all donations for a given people as a formatted array
     * @return json
     * @Route("/{id}/list/donations", name="list_donations", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getDonationsListAction(People $people)
    {
        $donationsData = [];
        foreach ($people->getDonations() as $donation) {
            $payment = $donation->getPayment();
            $formattedDonation = [
                'id' => $donation->getId(),
                'price' => $donation->getAmount(),
                'date' => $donation->getDonationDate()?->format('d/m/Y'),
                'origin' => $donation->getDonationOrigin()?->getLabel(),
                'payment' => [
                    'amount' => $payment->getAmount(),
                    'mean' => $payment->getType()->getLabel(),
                    'date_received' => $payment->getDateReceived()?->format('d/m/Y'),
                    'date_cashed' => $payment->getDateCashed()?->format('d/m/Y'),
                    'payer' => [
                        'id' => $payment->getPayer()->getId(),
                        'denomination' => $payment->getPayer()->getDenomination()->getLabel(),
                        'firstname' => $payment->getPayer()->getFirstName(),
                        'lastname' => $payment->getPayer()->getLastName(),
                    ],
                    'fiscal_receipt' => [
                        'fiscal_year' => $payment->getReceipt()->getYear(),
                        'order_code' => $payment->getReceipt()->getOrderCode(),
                    ],
                    'comment' => $payment->getComment(),
                ],
            ];

            $donationsData[] = $formattedDonation;
        }

        $response = new Response();
        $response->setContent(json_encode([
            'data' => $donationsData,
        ]));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * List all payments for a given people as a formatted array
     * @return json
     * @Route("/{id}/list/payments", name="list_payments", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPaymentsListAction(People $people)
    {
        $paymentsData = [];
        foreach ($people->getPayments() as $payment) {
            $usedForLabel = '';
            if (empty($payment->getDonation())) {
                $usedForLabel = sprintf(
                    'Adhésion (%s, %s €)',
                    $payment->getMembership()->getType()->getLabel(),
                    $payment->getMembership()->getType()->getDefaultAmount()
                );
            } else {
                if (!empty($payment->getMembership())) {
                    $usedForLabel = sprintf(
                        'Adhésion (%s, %s €) et don (%s €)',
                        $payment->getMembership()->getType()->getLabel(),
                        $payment->getMembership()->getType()->getDefaultAmount(),
                        $payment->getDonation()->getAmount()
                    );
                } else {
                    $usedForLabel = 'Don';
                }
            }
            $formattedDonation = [
                'id' => $payment->getId(),
                'usage' => $usedForLabel,
                'amount' => $payment->getAmount(),
                'mean' => $payment->getType()->getLabel(),
                'date_received' => $payment->getDateReceived()?->format('d/m/Y'),
                'date_cashed' => $payment->getDateCashed()?->format('d/m/Y'),
                'fiscal_year' => $payment->getReceipt()->getYear(),
                'order_code' => $payment->getReceipt()->getOrderCode(),
                'comment' => $payment->getComment(),
            ];

            $paymentsData[] = $formattedDonation;
        }

        $response = new Response();
        $response->setContent(json_encode([
            'data' => $paymentsData,
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
