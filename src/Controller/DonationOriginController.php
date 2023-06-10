<?php

namespace App\Controller;

use App\Entity\DonationOrigin;
use App\Form\DonationOriginType;
use App\Repository\DonationOriginRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/donation-origin")
 */
class DonationOriginController extends AbstractController
{
    /**
     * @Route("/", name="donation_origin_list", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function list(DonationOriginRepository $donationOriginRepository): Response
    {
        return $this->render('DonationOrigin/list.html.twig', [
            'donationOrigins' => $donationOriginRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="donation_origin_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $donationOrigin = new DonationOrigin();
        $form = $this->createForm(DonationOriginType::class, $donationOrigin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($donationOrigin);
            $entityManager->flush();

            return $this->redirectToRoute('donation_origin_list');
        }

        return $this->render('DonationOrigin/new.html.twig', [
            'donationOrigin' => $donationOrigin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="donation_origin_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(DonationOrigin $donationOrigin): Response
    {
        return $this->render('DonationOrigin/show.html.twig', [
            'donationOrigin' => $donationOrigin,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="donation_origin_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, DonationOrigin $donationOrigin): Response
    {
        $form = $this->createForm(DonationOriginType::class, $donationOrigin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('donation_origin_list');
        }

        return $this->render('DonationOrigin/edit.html.twig', [
            'donationOrigin' => $donationOrigin,
            'form' => $form->createView(),
        ]);
    }
}
