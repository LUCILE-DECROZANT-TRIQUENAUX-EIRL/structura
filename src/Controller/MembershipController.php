<?php

namespace App\Controller;

use App\Entity\People;
use App\Entity\Payment;
use App\Entity\Receipt;
use App\Entity\Donation;
use App\Entity\Membership;

use App\Form\MemberSelectionType;
use App\Form\MembershipFormType;

use App\FormDataObject\MemberSelectionFDO;
use App\FormDataObject\UpdateMembershipFDO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * MembershipController controller.
 *
 * @Route(path="/{_locale}/membership", requirements={"_locale"="en|fr"})
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
     * @Route("/new", name="membership_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function createAction(Request $request, TranslatorInterface $translator)
    {
        // Form setup
        $em = $this->getDoctrine()->getManager();

        $peopleWithNoActiveMembership = $em->getRepository(People::class)->findWithNoActiveMembership();

        $updateMembershipFDO = new UpdateMembershipFDO();

        $membershipCreationForm = $this->createForm(MembershipFormType::class, $updateMembershipFDO, [
            'selectablePeople' => $peopleWithNoActiveMembership
        ]);

        $membershipCreationForm->handleRequest($request);

        // Submit change
        if ($membershipCreationForm->isSubmitted() && $membershipCreationForm->isValid())
        {
            // Membership
            $membership = new Membership();

            $membership->setMembers($updateMembershipFDO->getMembers());
            $membership->setType($updateMembershipFDO->getMembershipType());
            $membership->setAmount($updateMembershipFDO->getMembershipAmount());
            $membership->setDateStart($updateMembershipFDO->getMembershipDateStart());
            $membership->setDateEnd($updateMembershipFDO->getMembershipDateEnd());
            $membership->setFiscalYear($updateMembershipFDO->getMembershipFiscalYear());
            $membership->setComment($updateMembershipFDO->getMembershipComment());

            // Payment
            $payment = new Payment();

            $payment->setType($updateMembershipFDO->getPaymentType());
            $payment->setAmount($updateMembershipFDO->getPaymentAmount());
            $payment->setDateReceived($updateMembershipFDO->getPaymentDateReceived());
            $payment->setDateCashed($updateMembershipFDO->getPaymentDateCashed());
            if ($payment->getType()->isBankneeded())
            {
                $payment->setBank($updateMembershipFDO->getBank());
            }
            else
            {
                $payment->setBank(null);
            }
            $payment->setPayer($updateMembershipFDO->getPayer());
            $payment->setMembership($membership);

            // Donation
            $donationAmount = $updateMembershipFDO->getDonationAmount();

            // If donation is also done with the membership
            if (!empty($donationAmount)) {
                $donation = new Donation();

                $donation->setAmount($donationAmount);
                $donation->setDonator($updateMembershipFDO->getPayer());
                $donation->setDonationDate($payment->getDateReceived());

                $payment->setDonation($donation);

                $em->persist($donation);
            }

            $em->persist($membership);
            $em->persist($payment);

            // Receipt
            $thisYear = (new \DateTime())->format('Y');
            $lastOrderNumber = $em->getRepository(Receipt::class)
                    ->findLastOrderNumberForAYear($thisYear);

            $receipt = new Receipt();

            $receipt->setPayment($payment);
            $receipt->setOrderNumber($lastOrderNumber + 1);

            $receipt->setYear($thisYear);
            $receipt->setOrderCode();

            $em->persist($receipt);

            $em->flush();

            $this->addFlash(
                'success', $translator->trans('L\'adhésion a bien été enregistrée.')
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

    /**
     * Create a new membership
     * @return views
     * @Route("/{id}/edit", name="membership_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function editAction(Membership $membership, Request $request, TranslatorInterface $translator)
    {
        // Form setup
        $em = $this->getDoctrine()->getManager();

        $peopleWithNoActiveMembership = $em->getRepository(People::class)->findWithNoActiveMembership();
        $oldMembers = $membership->getMembers()->toArray();
        $selectablePeople = array_merge($peopleWithNoActiveMembership, $oldMembers);

        $updateMembershipFDO = new UpdateMembershipFDO($membership);

        $membershipEditForm = $this->createForm(MembershipFormType::class, $updateMembershipFDO, [
            'selectablePeople' => $selectablePeople
        ]);

        $membershipEditForm->handleRequest($request);

        // Submit change
        if ($membershipEditForm->isSubmitted() && $membershipEditForm->isValid())
        {
            $removedMembers = [];

            foreach($oldMembers as $member)
            {
                $memberToRemove = $em->getReference(People::Class, $member->getId());
                $memberToRemove->removeMembership($membership);
                $removedMembers[] = $memberToRemove;

                $em->persist($memberToRemove);
            }

            $membership->setMembers($updateMembershipFDO->getMembers());
            $membership->setType($updateMembershipFDO->getMembershipType());
            $membership->setAmount($updateMembershipFDO->getMembershipAmount());
            $membership->setDateStart($updateMembershipFDO->getMembershipDateStart());
            $membership->setDateEnd($updateMembershipFDO->getMembershipDateEnd());
            $membership->setFiscalYear($updateMembershipFDO->getMembershipFiscalYear());
            $membership->setComment($updateMembershipFDO->getMembershipComment());

            // Payment
            $payment = $membership->getPayment();

            $payment->setType($updateMembershipFDO->getPaymentType());
            $payment->setAmount($updateMembershipFDO->getPaymentAmount());
            $payment->setDateReceived($updateMembershipFDO->getPaymentDateReceived());
            $payment->setDateCashed($updateMembershipFDO->getPaymentDateCashed());
            if ($payment->getType()->isBankneeded())
            {
                $payment->setBank($updateMembershipFDO->getBank());
            }
            else
            {
                $payment->setBank(null);
            }
            $payment->setPayer($updateMembershipFDO->getPayer());

            // Donation
            $donationAmount = $updateMembershipFDO->getDonationAmount();
            $donation = $payment->getDonation();

            // If a donation was existing and has been modified
            if ($donation != null && !empty($donationAmount))
            {
                $donation->setAmount($donationAmount);
                $donation->setDonator($updateMembershipFDO->getPayer());
                $donation->setDonationDate($payment->getDateReceived());

                $em->persist($donation);
            }
            // If a donation was existing and has been removed
            else if ($donation != null && empty($donationAmount))
            {
                $em->remove($donation);
            }
            // If there was no donation and it has been added
            else if ($donation == null && !empty($donationAmount))
            {
                $donation = new Donation();

                $donation->setAmount($donationAmount);
                $donation->setDonator($updateMembershipFDO->getPayer());
                $donation->setDonationDate($payment->getDateReceived());

                $payment->setDonation($donation);

                $em->persist($donation);
            }

            $em->persist($membership);
            $em->persist($payment);

            $em->flush();

            $this->addFlash(
                'success', $translator->trans('Les informations de l\'adhésion ont bien été modifiées.')
            );

            return $this->redirectToRoute('membership_show', array('id' => $membership->getId()));
        }

        return $this->render('Membership/edit.html.twig', [
            'membership' => $membership,
            'membership_edit_form' => $membershipEditForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="membership_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Membership $membership, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$membership->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $payment = $membership->getPayment();
            if ($membership->getAmount() === $payment->getAmount())
            {
                $entityManager->remove($payment);
                $entityManager->remove($membership);
            }
            else
            {
                $payment->setAmount($payment->getAmount() - $membership->getAmount());
                $entityManager->persist($payment);
                $entityManager->remove($membership);
            }


            $entityManager->flush();
            $this->addFlash(
                    'success', $translator->trans('Adhésion supprimée.')
            );
        } else {
            $this->addFlash(
                    'danger', $translator->trans('Une erreur est survenue.')
            );
        }

        return $this->redirectToRoute('membership_list');
    }
}