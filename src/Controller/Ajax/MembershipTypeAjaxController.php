<?php

namespace App\Controller\Ajax;

use App\Entity\MembershipType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * @Route("/ajax/membership-type", name="ajax_membership-type_")
 */
class MembershipTypeAjaxController extends AbstractFOSRestController
{
    /**
     * @Route("/{id}", name="show", requirements = {"id"="\d+"})
     * @View()
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getMembershipTypeAction(MembershipType $membershipType)
    {
        return $this->handleView($this->view($membershipType));
    }
}
