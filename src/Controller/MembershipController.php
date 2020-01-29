<?php

namespace App\Controller;

use App\Entity\People;
use App\Entity\Payment;
use App\Entity\Donation;
use App\Entity\Membership;

use App\Form\MemberSelectionType;
use App\Form\MembershipCreationType;

use App\FormDataObject\MemberSelectionFDO;
use App\FormDataObject\CreateMembershipFDO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        $memberSelectionFDO = new MemberSelectionFDO();

        $memberSelectionForm = $this->createForm(MemberSelectionType::class, $memberSelectionFDO, [
            'peoples' => $peoples
        ]);

        $options = $memberSelectionForm->get('newMembers')->getConfig()->getOptions();
        $choices = $options['choices'];

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
     * Create a new membership
     * @return views
     * @Route("/new", name="member_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function createAction(Request $request)
    {
        // Form setup
        $em = $this->getDoctrine()->getManager();

        $peopleWithNoActiveMembership = $em->getRepository(People::class)->findWithNoActiveMembership();

        $createMembershipFDO = new CreateMembershipFDO();

        $membershipCreationForm = $this->createForm(MembershipCreationType::class, $createMembershipFDO, [
            'peopleWithNoActiveMembership' => $peopleWithNoActiveMembership
        ]);

        $membershipCreationForm->handleRequest($request);

        // Submit change
        if ($membershipCreationForm->isSubmitted() && $membershipCreationForm->isValid())
        {
            // Membership
            $membership = new Membership();

            $membership->setMembers($createMembershipFDO->getMembers());
            $membership->setType($createMembershipFDO->getMembershipType());
            $membership->setAmount($createMembershipFDO->getMembershipAmount());
            $membership->setDateStart($createMembershipFDO->getMembershipDateStart());
            $membership->setDateEnd($createMembershipFDO->getMembershipDateEnd());
            $membership->setComment($createMembershipFDO->getMembershipComment());

            // Payment
            $payment = new Payment();

            $payment->setType($createMembershipFDO->getPaymentType());
            $payment->setAmount($createMembershipFDO->getPaymentAmount());
            $payment->setDateReceived($createMembershipFDO->getPaymentDateReceived());
            $payment->setDateCashed($createMembershipFDO->getPaymentDateCashed());

            $payment->setPayer($createMembershipFDO->getPayer());
            $payment->setMembership($membership);

            // If donation is also done with the membership
            if ($createMembershipFDO->getIsMembershipAndDonation()) {
                $donation = new Donation();

                $donation->setAmount($createMembershipFDO->getDonationAmount());
                $donation->setDonator($createMembershipFDO->getPayer());
                $donation->setDonationDate($payment->getDateReceived());

                $payment->setDonation($donation);

                $em->persist($donation);
            }

            $em->persist($membership);
            $em->persist($payment);

            $em->flush();

            $this->addFlash(
                'success', 'L\'adhésion a bien été enregistrée.'
            );

            return $this->redirectToRoute('membership_show', array('id' => $membership->getId()));
        }

        return $this->render('Membership/new.html.twig', [
            'membership_creation_form' => $membershipCreationForm->createView()
        ]);
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