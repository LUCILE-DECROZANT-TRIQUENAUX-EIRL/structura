<?php

namespace App\Controller\Ajax;

use App\Entity\MembershipType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @Route("/ajax/membership-type", name="ajax_membership-type_")
 */
class MembershipTypeAjaxController extends FOSRestController
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
    public function getMembershipTypeAction(MembershipType $membershipType)
    {
        return $this->handleView($this->view($membershipType));
    }
}