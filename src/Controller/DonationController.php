<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Form\DonationType;
use App\Repository\DonationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/donation")
 */
class DonationController extends AbstractController
{
    /**
     * @Route("/", name="donation_index", methods={"GET"})
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
        $donation = new Donation();
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($donation);
            $entityManager->flush();

            return $this->redirectToRoute('donation_index');
        }

        return $this->render('Donation/new.html.twig', [
            'donation' => $donation,
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
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('donation_index');
        }

        return $this->render('Donation/edit.html.twig', [
            'donation' => $donation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="donation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Donation $donation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($donation);
            $entityManager->flush();
            $this->addFlash(
                    'success', 'Don supprimé.'
            );
        } else {
            $this->addFlash(
                    'danger', 'Une erreur est survenue.'
            );
        }

        return $this->redirectToRoute('donation_index');
    }
}
