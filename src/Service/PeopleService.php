<?php

namespace App\Service;

use App\FormDataObject\FilterPeopleFDO;
use App\Repository\DonationOriginRepository;
use App\Repository\PeopleRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class containing methods used to handle people.
 */
class PeopleService
{
    public function __construct(
        private PeopleRepository $peopleRepository,
        private DonationOriginRepository $donationOriginRepository,
    ) {
    }

    public function filterPeopleFromRequest(Request $request, bool $isDematerializedNewsletter = false)
    {
        // Creating an FDO with default value
        $filterPeopleFDO = new FilterPeopleFDO();

        // Membership years param override
        $membershipYears = $request->get('adhesion_annees');
        if ($membershipYears) {
            $filterPeopleFDO->setMembershipYears($membershipYears);
        }

        // Departments param override
        $departments = $request->get('departements');
        if ($departments) {
            $filterPeopleFDO->setDepartments($departments);
        }

        // Physical mail only param override
        $isPhysicalMailOnly = $request->get('courrier_uniquement');
        if ($isPhysicalMailOnly) {
            $filterPeopleFDO->setPhysicalMailOnly($isPhysicalMailOnly);
        }

        // Dematerialized newsletter
        if ($isDematerializedNewsletter) {
            $filterPeopleFDO->setDematerializedNewsletter($isDematerializedNewsletter);
        }

        // Donations years param override
        $donationYears = $request->get('don_annees');
        if ($donationYears) {
            $filterPeopleFDO->setDonationYears($donationYears);
        }

        // Donations origins param override
        $donationOrigins = $request->get('don_origine');
        if ($donationOrigins) {
            $filterPeopleFDO->setDonationOrigins(
                $this->donationOriginRepository->findByIds($donationOrigins)
            );
        }

        // Filtering people for the preview
        $people = $this->peopleRepository->filterForTags([
            'membership_years' => $filterPeopleFDO->getMembershipYears(),
            'departments' => $filterPeopleFDO->getDepartments(),
            'donation_years' => $filterPeopleFDO->getDonationYears(),
            'donation_origins' => $filterPeopleFDO->getDonationOrigins(),
            'physical_mail_only' => $filterPeopleFDO->isPhysicalMailOnly(),
            'dematerialized_newsletter' => $filterPeopleFDO->isDematerializedNewsletter(),
        ]);

        return [
            $people,
            $filterPeopleFDO,
        ];
    }
}