<?php

namespace App\Controller;

use App\Entity\Association;
use App\Form\AssociationType;
use App\FormDataObject\AssociationNameFDO;
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
        $entityManager = $this->getDoctrine()->getManager();
        $association = $entityManager->getRepository(Association::class)->findOneById(1);
        if (empty($association)) {
            $association = new Association();
        }
        return $this->render('Association/show.html.twig', [
            'association' => $association,
        ]);
    }

    /**
     * @Route("/edit-name", name="association_edit_name", methods={"GET","POST"})
     */
    public function editName(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // use a FDO to only edit the name of the association
        $associationNameFDO = new AssociationNameFDO();
        $association = $entityManager->getRepository(Association::class)->findOneById(1);
        if (empty($association)) {
            $association = new Association();
        }
        // display the current name in the form
        $associationNameFDO->setName($association->getName());

        $form = $this->createForm(AssociationType::class, $associationNameFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $association->setName($associationNameFDO->getName());
            $entityManager->persist($association);
            $entityManager->flush();

            $this->addFlash(
                'success', 'Les informations ont bien été enregistrées'
            );
            return $this->redirectToRoute('association_index');
        }

        return $this->render('Association/edit.html.twig', [
            'association' => $association,
            'form' => $form->createView(),
        ]);
    }
}
