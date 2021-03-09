<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\Receipt;
use App\Form\DonationType;
use App\FormDataObject\UpdateDonationFDO;
use App\Repository\DonationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(path="/{_locale}/donation", requirements={"_locale"="en|fr"})
 */
class DonationController extends AbstractController
{
    /**
     * @Route("/", name="donation_list", methods={"GET"})
     */
    public function listAction(DonationRepository $donationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('Donation/list.html.twig', [
            'donations' => $em->getRepository(Donation::class)->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="donation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $updateDonationFDO = new UpdateDonationFDO();

        // Preselect check payment type
        $em = $this->getDoctrine()->getManager();
        $checkPaymentType = $em->getRepository(PaymentType::class)->find(4);
        $updateDonationFDO->setPaymentType($checkPaymentType);

        $form = $this->createForm(DonationType::class, $updateDonationFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $donator = $updateDonationFDO->getDonator();

            // -- DONATION -- //
            $donation = new Donation();

            $donation->setAmount($updateDonationFDO->getAmount());
            $donation->setDonator($donator);
            $donation->setDonationDate($updateDonationFDO->getDonationDate());


            // -- PAYMENT -- //
            $payment = new Payment();

            $payment->setPayer($donator);
            $payment->setType($updateDonationFDO->getPaymentType());
            $payment->setAmount($updateDonationFDO->getAmount());
            $payment->setDateReceived($updateDonationFDO->getDonationDate());
            $payment->setDateCashed($updateDonationFDO->getCashedDate());
            $payment->setComment($updateDonationFDO->getComment());

            $em->persist($payment);

            // Putting data used for the receipt in vars
            $thisYear = (new \DateTime())->format('Y');
            $lastOrderNumber = $em->getRepository(Receipt::class)
                    ->findLastOrderNumberForAYear($thisYear);

            // -- RECEIPT -- //
            $receipt = new Receipt();

            $receipt->setPayment($payment);
            $receipt->setOrderNumber($lastOrderNumber + 1);
            $receipt->setYear($thisYear);
            $receipt->setOrderCode();

            $em->persist($receipt);

            $donation->setPayment($payment);
            $em->persist($donation);

            $em->flush();

            return $this->redirectToRoute('donation_show', ['id' => $donation->getId()]);
        }

        return $this->render('Donation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="donation_show", methods={"GET"})
     */
    public function show(Donation $donation): Response
    {
        return $this->render('Donation/show.html.twig', [
            'donation' => $donation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="donation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Donation $donation): Response
    {
        $updatePeopleDataFDO = new UpdateDonationFDO($donation);

        $form = $this->createForm(DonationType::class, $updatePeopleDataFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $donator = $updatePeopleDataFDO->getDonator();

            $donation->setAmount($updatePeopleDataFDO->getAmount());
            $donation->setDonator($donator);
            $donation->setDonationDate($updatePeopleDataFDO->getDonationDate());

            $payment = $donation->getPayment();

            $payment->setPayer($donator);
            $payment->setType($updatePeopleDataFDO->getPaymentType());
            $payment->setAmount($updatePeopleDataFDO->getAmount());
            $payment->setDateReceived($updatePeopleDataFDO->getDonationDate());
            $payment->setDateCashed($updatePeopleDataFDO->getCashedDate());

            $em->persist($payment);
            $em->persist($donation);

            $em->flush();

            return $this->redirectToRoute('donation_show', ['id' => $donation->getId()]);
        }

        return $this->render('Donation/edit.html.twig', [
            'donation' => $donation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="donation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Donation $donation, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($donation);
            $entityManager->flush();
            $this->addFlash(
                    'success', $translator->trans('Don supprimé.')
            );
        } else {
            $this->addFlash(
                    'danger', $translator->trans('Une erreur est survenue.')
            );
        }

        return $this->redirectToRoute('donation_list');
    }
}
