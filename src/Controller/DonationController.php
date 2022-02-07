<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\People;
use App\Entity\Receipt;
use App\Form\DonationType;
use App\FormDataObject\UpdateDonationFDO;
use App\Repository\DonationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function listAction(DonationRepository $donationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('Donation/list.html.twig', [
            'donations' => $donationRepository->findOrderedDonations()
        ]);
    }

    /**
     * @Route("/new", name="donation_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $createDonationFDO = new UpdateDonationFDO();

        // Preselect member if optional parameter is not null
        $personId = $request->get('person-id');

        if (!is_null($personId))
        {
            $newDonator = $em->getRepository(People::class)->find($personId);

            if (is_null($newDonator))
            {
                $this->addFlash(
                    'danger', 'La personne pré-sélectionnée n\'existe pas en base de donnée.'
                );
            }
            else
            {
                $createDonationFDO->setDonator($newDonator);
            }
        }

        // Preselect check payment type
        $checkPaymentType = $em->getRepository(PaymentType::class)->find(4);
        $createDonationFDO->setPaymentType($checkPaymentType);

        $form = $this->createForm(DonationType::class, $createDonationFDO);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            dump((string) $form->getErrors(true, false));die;
        }

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $donator = $createDonationFDO->getDonator();
            $donationDate = $createDonationFDO->getDonationDate();

            // -- DONATION -- //
            $donation = new Donation();

            $donation->setAmount($createDonationFDO->getAmount());
            $donation->setDonator($donator);
            $donation->setDonationDate($donationDate);

            // -- PAYMENT -- //
            $payment = new Payment();

            $payment->setPayer($donator);
            $payment->setType($createDonationFDO->getPaymentType());
            $payment->setAmount($createDonationFDO->getAmount());
            $payment->setDateReceived($createDonationFDO->getDonationDate());
            $payment->setDateCashed($createDonationFDO->getCashedDate());
            if ($payment->getType()->isBankneeded())
            {
                $payment->setBank($createDonationFDO->getBank());
                $payment->setCheckNumber($createDonationFDO->getCheckNumber());
            }
            else
            {
                $payment->setBank(null);
                $payment->setCheckNumber(null);
            }
            $payment->setComment($createDonationFDO->getComment());

            $em->persist($payment);

            // Putting data used for the receipt in vars
            $donationYear = $donationDate->format('Y');
            $lastOrderNumber = $em->getRepository(Receipt::class)
                    ->findLastOrderNumberForAYear($donationYear);

            // -- RECEIPT -- //
            $receipt = new Receipt();

            $receipt->setPayment($payment);
            $receipt->setOrderNumber($lastOrderNumber + 1);
            $receipt->setYear($donationYear);
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
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function show(Donation $donation): Response
    {
        return $this->render('Donation/show.html.twig', [
            'donation' => $donation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="donation_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function edit(Request $request, Donation $donation): Response
    {
        $updateDonationFDO = new UpdateDonationFDO($donation);

        $form = $this->createForm(DonationType::class, $updateDonationFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $donator = $updateDonationFDO->getDonator();

            $donation->setAmount($updateDonationFDO->getAmount());
            $donation->setDonator($donator);
            $donation->setDonationDate($updateDonationFDO->getDonationDate());

            $payment = $donation->getPayment();

            $payment->setPayer($donator);
            $payment->setType($updateDonationFDO->getPaymentType());
            $payment->setAmount($updateDonationFDO->getAmount());
            $payment->setDateReceived($updateDonationFDO->getDonationDate());
            $payment->setDateCashed($updateDonationFDO->getCashedDate());
            if ($payment->getType()->isBankneeded())
            {
                $payment->setBank($updateDonationFDO->getBank());
                $payment->setCheckNumber($updateDonationFDO->getCheckNumber());
            }
            else
            {
                $payment->setBank(null);
                $payment->setCheckNumber(null);
            }
            $payment->setComment($updateDonationFDO->getComment());

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
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function delete(Request $request, Donation $donation, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $payment = $donation->getPayment();
            if ($donation->getAmount() === $payment->getAmount())
            {
                $entityManager->remove($payment);
                $entityManager->remove($donation);
            }
            else
            {
                $payment->setAmount($payment->getAmount() - $donation->getAmount());
                $entityManager->persist($payment);
                $entityManager->remove($donation);
            }

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
