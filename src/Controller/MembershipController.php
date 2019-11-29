<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\People;
use App\Entity\Membership;
use App\Form\MemberSelectionType;
use App\FormDataObject\MemberSelectionFDO;

/**
 * MembershipController controller.
 *
 * @Route("membership")
 */
class MembershipController extends AbstractController
{
    /**
     * @Route("/member-selection", name="member-selection")
     */
    public function memberSelectionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $peoples = $em->getRepository(People::class)->findWithNoActiveMembership();

        // dump($peoples); die;

        $memberSelectionFDO = new MemberSelectionFDO();

        $memberSelectionForm = $this->createForm(MemberSelectionType::class, $memberSelectionFDO, [
            'peoples' => $peoples
        ]);

        $options = $memberSelectionForm->get('newMembers')->getConfig()->getOptions();
        $choices = $options['choices'];

        //  dump($choices); die;

        $memberSelectionForm->handleRequest($request);

        // Submit change
        if ($memberSelectionForm->isSubmitted() && $memberSelectionForm->isValid())
        {
            dump($memberSelectionFDO->getNewMembers());
            die();
        }

        return $this->render('Membership/member-selection.html.twig', [
            'peoples' => $choices,
            'member_selection_form' => $memberSelectionForm->createView()
        ]);
    }

    /**
     * Lists all memberships entities.
     * @return views
     * @Route(path="/", name="membership_list", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $members = $em->getRepository(People::class)->findWithActiveMembership();

        return $this->render('Membership/list.html.twig', array(
            'members' => $members,
        ));
    }

    /**
     * Finds and displays the memberships of a people.
     * @return views
     * @param People $people The user to find to display memberships.
     * @Route("/{id}", name="membership_show", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showAction(Membership $membership)
    {
        return $this->render('Membership/show.html.twig', array(
            'membership' => $membership,
        ));
    }

}
