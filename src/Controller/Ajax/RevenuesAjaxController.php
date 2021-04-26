<?php

namespace App\Controller\Ajax;

use App\Entity\Membership;
use App\Entity\Donation;
use App\Entity\People;
use App\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @Route("/ajax/revenues", name="ajax_revenues_")
 */
class RevenuesAjaxController extends FOSRestController
{

    /**
     * Return datasets containing the membership revenues, donation revenues
     * and their sum by month, on the last 12 months.
     *
     * @return json
     * @Route("/recap", name="recap", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getRevenuesRecapAction()
    {
        $em = $this->getDoctrine()->getManager();

        $donationRepository = $em->getRepository(Donation::class);
        $membershipRepository = $em->getRepository(Membership::class);
        $paymentRepository = $em->getRepository(Payment::class);

        $donationRevenues = $donationRepository->getRevenuesRecap();
        $membershipRevenues = $membershipRepository->getRevenuesRecap();
        $cumulatedRevenues = $paymentRepository->getRevenuesRecap();

        $labels = [];
        $formatedDonationRevenues = [];
        $formatedMembershipRevenues = [];
        $formatedCumulatedRevenues = [];


        // Initialize formated data arrays
        $currentDate = new \DateTime();
        for ($i = 12; $i > 0; $i--)
        {
            $monthToSubstract = sprintf("-%s months", $i - 1);
            $monthDate = strtotime($monthToSubstract, $currentDate->getTimestamp());

            // Labels for x axis
            $labels[] = date('M Y', $monthDate);

            // Data for curves
            $dateFormatedAsRequests = date('Y-m', $monthDate);
            $formatedDonationRevenues[$dateFormatedAsRequests] = 0;
            $formatedMembershipRevenues[$dateFormatedAsRequests] = 0;
            $formatedCumulatedRevenues[$dateFormatedAsRequests] = 0;
        }

        // Fill formated data arrays
        foreach ($donationRevenues as $donationMontlyRevenue)
        {
            $formatedDonationRevenues[$donationMontlyRevenue['date']] = (float) $donationMontlyRevenue['revenues'];
        }

        foreach ($membershipRevenues as $membershipMontlyRevenue)
        {
            $formatedMembershipRevenues[$membershipMontlyRevenue['date']] = (float) $membershipMontlyRevenue['revenues'];
        }

        foreach ($cumulatedRevenues as $cumulatedMontlyRevenue)
        {
            $formatedCumulatedRevenues[$cumulatedMontlyRevenue['date']] = (float) $cumulatedMontlyRevenue['revenues'];
        }

        $recap['labels'] = $labels;
        $recap['donation_revenues'] = array_values($formatedDonationRevenues);
        $recap['membership_revenues'] = array_values($formatedMembershipRevenues);
        $recap['cumulated_revenues'] = array_values($formatedCumulatedRevenues);

        $response = new Response();
        $response->setContent(json_encode([
            'data' => $recap,
        ]));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
