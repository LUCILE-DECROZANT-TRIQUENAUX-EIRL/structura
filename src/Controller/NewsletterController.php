<?php

namespace App\Controller;

use App\Form\FilterPeopleType;
use App\Repository\DonationRepository;
use App\Repository\MembershipRepository;
use App\Service\PeopleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/{_locale}/newsletter", requirements={"_locale"="en|fr"})
 */
class NewsletterController extends AbstractController
{
    /**
     * @Route("/", name="newsletter_dematerialized", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function dematarializedAction(
        Request $request,
        PeopleService $peopleService,
        MembershipRepository $membershipRepository,
        DonationRepository $donationRepository,
    ): Response
    {
        list($people, $filterPeopleFDO) = $peopleService->filterPeopleFromRequest($request, true);

        // Getting all years for which there is memberships
        // It will help setup the membership years filter when generating the form
        $options['availableMembershipYears'] = $membershipRepository->getAvailableFiscalYears();

        // Getting all years for which there is memberships
        // It will help setup the membership years filter when generating the form
        $options['availableDonationYears'] = $donationRepository->getAvailableDonationsYears();

        // Form creation
        $dematerializedNewsletterForm = $this->createForm(
            FilterPeopleType::class,
            $filterPeopleFDO,
            $options
        );
        
        return $this->render('Newsletter/dematarialized.html.twig', [
            'people' => $people,
            'dematerialized_newsletter_form' => $dematerializedNewsletterForm->createView(),
        ]);
    }
}
