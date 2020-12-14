<?php

namespace App\Controller;

use App\Entity\Bank;
use App\Form\BankType;
use App\Repository\BankRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/bank")
 */
class BankController extends AbstractController
{
    /**
     * @Route("/", name="bank_list", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function list(BankRepository $bankRepository): Response
    {
        return $this->render('Bank/list.html.twig', [
            'banks' => $bankRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="bank_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $bank = new Bank();
        $form = $this->createForm(BankType::class, $bank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bank);
            $entityManager->flush();

            return $this->redirectToRoute('bank_list');
        }

        return $this->render('Bank/new.html.twig', [
            'bank' => $bank,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bank_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Bank $bank): Response
    {
        return $this->render('Bank/show.html.twig', [
            'bank' => $bank,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bank_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Bank $bank): Response
    {
        $form = $this->createForm(BankType::class, $bank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bank_list');
        }

        return $this->render('Bank/edit.html.twig', [
            'bank' => $bank,
            'form' => $form->createView(),
        ]);
    }
}
