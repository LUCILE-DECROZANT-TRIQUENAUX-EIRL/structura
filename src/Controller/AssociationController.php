<?php

namespace App\Controller;

use App\Entity\Association;
use App\Form\AssociationType;
use App\Repository\AssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/association")
 */
class AssociationController extends AbstractController
{
    /**
     * @Route("/", name="association_index", methods={"GET"})
     */
    public function show(AssociationRepository $associationRepository): Response
    {
        $association = new Association();
        return $this->render('Association/show.html.twig', [
            'association' => $association,
        ]);
    }

    /**
     * @Route("/edit", name="association_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
        $association = new Association();

        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('association_index');
        }

        return $this->render('Association/edit.html.twig', [
            'association' => $association,
            'form' => $form->createView(),
        ]);
    }
}
