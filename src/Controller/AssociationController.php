<?php

namespace App\Controller;

use App\Entity\Association;
use App\Form\AssociationType;
use App\Form\AssociationLogoType;
use App\FormDataObject\AssociationLogoFDO;
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

    /**
     * @Route("/upload-logo", name="association_upload_logo", methods={"GET","POST"})
     */
    public function uploadLogoAction(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        // use a FDO to only edit the name of the association
        $associationLogoFDO = new AssociationLogoFDO();
        $association = $entityManager->getRepository(Association::class)->findOneById(1);
        if (empty($association)) {
            $association = new Association();
        }

        $form = $this->createForm(AssociationLogoType::class, $associationLogoFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $associationLogoFDO->getLogo();
            $name = 'logo.' . $logo->guessExtension();
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/images/uploads';
            $logo->move(
                    $uploadDirectory,
                    $name
            );

            $association->setLogoFilename('images/uploads/' . $name);
            $entityManager->persist($association);
            $entityManager->flush();

            $this->addFlash(
                'success', 'Le logo a bien été téléversé.'
            );
            return $this->redirectToRoute('association_index');
        }

        return $this->render('Association/edit-logo.html.twig', [
            'association' => $association,
            'form' => $form->createView(),
        ]);
    }
}
