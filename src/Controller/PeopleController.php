<?php

/**
 * People controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\People;
use App\Entity\Address;
use App\Entity\Receipt;
use App\Entity\PeopleType;
use App\Form\PeopleType as PeopleForm;
use App\Form\GenerateTaxReceiptFromYearType;
use App\Service\ReceiptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use App\FormDataObject\UpdatePeopleDataFDO;
use App\FormDataObject\GenerateTaxReceiptFromYearFDO;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * People controller.
 *
 * @Route(path="/{_locale}/people", requirements={"_locale"="en|fr"})
 */
class PeopleController extends AbstractController {

    /**
     * Lists all people entities.
     * @return views
     * @Route(path="/", name="people_list", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function listAction() {
        $em = $this->getDoctrine()->getManager();

        $peoples = $em->getRepository(People::class)->findAll();

        $deleteForms = [];

        foreach ($peoples as $people) {
            $deleteForm = $this->createDeleteForm($people);
            $deleteForms[$people->getId()] = $deleteForm->createView();
        }


        return $this->render('People/list.html.twig', array(
                'peoples' => $peoples,
                'people_deletion_forms' => $deleteForms,
        ));
    }

    /**
     * Creates a new people entity.
     * @return views
     * @param Request $request The request.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/new", name="people_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator) {
        $updatePeopleDataFDO = new UpdatePeopleDataFDO();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PeopleForm::class, $updatePeopleDataFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $people = new People();

            $people->setDenomination($updatePeopleDataFDO->getDenomination());
            $people->setFirstName($updatePeopleDataFDO->getFirstName());
            $people->setLastName($updatePeopleDataFDO->getLastName());

            $type = $entityManager->getRepository(PeopleType::class)->findOneBy([
                'code' => PeopleType::CONTACT_CODE,
            ]);
            if ($updateMemberDataFDO->isContact())
            {
                $individual->addType($type);
            }
            else
            {
                $individual->removeType($type);

            }

            if ($updatePeopleDataFDO->getAddresses()['__name__'] === null) {
                $address = new Address();
                $people->setAddresses([$address]);
            } else {
                $address = $updatePeopleDataFDO->getAddresses()['__name__'];
                $people->setAddresses([$address]);
            }

            if ($updatePeopleDataFDO->getCellPhoneNumber() !== null) {
                $people->setCellPhoneNumber($updatePeopleDataFDO->getCellPhoneNumber());
            }

            if ($updatePeopleDataFDO->getHomePhoneNumber() !== null) {
                $people->setHomePhoneNumber($updatePeopleDataFDO->getHomePhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkPhoneNumber() !== null) {
                $people->setWorkPhoneNumber($updatePeopleDataFDO->getWorkPhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkFaxNumber() !== null) {
                $people->setWorkFaxNumber($updatePeopleDataFDO->getWorkFaxNumber());
            }

            if ($updatePeopleDataFDO->getObservations() !== null) {
                $people->setObservations($updatePeopleDataFDO->getObservations());
            }

            if ($updatePeopleDataFDO->getSensitiveObservations() !== null && $this->isGranted('ROLE_GESTION_SENSIBLE')) {
                $people->setSensitiveObservations($updatePeopleDataFDO->getSensitiveObservations());
            }

            if ($updatePeopleDataFDO->getEmailAddress() !== null) {
                $people->setEmailAddress($updatePeopleDataFDO->getEmailAddress());
            }

            if ($updatePeopleDataFDO->getIsReceivingNewsletter() !== null) {
                $people->setIsReceivingNewsletter($updatePeopleDataFDO->getIsReceivingNewsletter());
            }

            if ($updatePeopleDataFDO->getNewsletterDematerialization() !== null) {
                $people->setNewsletterDematerialization($updatePeopleDataFDO->getNewsletterDematerialization());
            }

            $em->persist($address);
            $em->persist($people);
            $em->flush();

            $userTranslation = $translator->trans('L\'utilisateurice');
            $hasBeenCreatedTranslation = $translator->trans('a été créé.e');

            $this->addFlash(
                'success', sprintf('%s <strong>%s %s</strong> %s',
                    $userTranslation,
                    $updatePeopleDataFDO->getFirstName(),
                    $updatePeopleDataFDO->getLastName(),
                    $hasBeenCreatedTranslation
                )
            );

            return $this->redirectToRoute('people_show', array('id' => $people->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $userTranslation = $translator->trans('L\'utilisateurice');
            $couldntBeCreatedTranslation = $translator->trans('n\'a pas pu être créé.e');

            $this->addFlash(
                'danger', sprintf('%s <strong>%s</strong> %s',
                    $userTranslation,
                    $updatePeopleDataFDO->getFirstName(),
                    $couldntBeCreatedTranslation
                )
            );
        }

        return $this->render('People/new.html.twig', array(
                'people' => $updatePeopleDataFDO,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a People entity.
     * @return views
     * @param People $people The user to display.
     * @Route("/{id}", name="people_show", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showAction(People $people) {
        $deleteForm = $this->createDeleteForm($people);

        return $this->render('People/show.html.twig', array(
                'people' => $people,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a People memberships.
     *
     * @return views
     * @param People $individual The user corresponding to the wanted memberships.
     * @Route("/{id}/memberships", name="people_memberships_show", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showMembershipsAction(People $individual) {
        return $this->render('People/show-memberships.html.twig', [
                'people' => $individual,
                'member' => $individual,
        ]);
    }

    /**
     * Displays a form to edit an existing People entity.
     * @return views
     * @param Request $request The request.
     * @param People $people The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/edit", name="people_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function editAction(Request $request, People $people, UserPasswordEncoderInterface $passwordEncoder,TranslatorInterface $translator) {
        $updatePeopleDataFDO = UpdatePeopleDataFDO::fromPeople($people);

        $entityManager = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($people);
        $editForm = $this->createForm(PeopleForm::class, $updatePeopleDataFDO);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Get the existing people to keep the sensible data it has if necessary
            $people = $entityManager->getRepository(People::class)->findOneBy([
                'id' => $people->getId(),
            ]);

            $people->setDenomination($updatePeopleDataFDO->getDenomination());
            $people->setFirstName($updatePeopleDataFDO->getFirstName());
            $people->setLastName($updatePeopleDataFDO->getLastName());

            $type = $entityManager->getRepository(PeopleType::class)->findOneBy([
                'code' => PeopleType::CONTACT_CODE,
            ]);
            if ($updateMemberDataFDO->isContact())
            {
                $individual->addType($type);
            }
            else
            {
                $individual->removeType($type);

            }

            if ($updatePeopleDataFDO->getAddresses() === null)
            {
                $address = new Address();
                $people->setAddresses([$address]);
            }
            else
            {
                $addresses = [];

                foreach ($updatePeopleDataFDO->getAddresses() as $address)
                {
                    $entityManager->persist($address);
                    $addresses[] = $address;
                }

                $people->setAddresses($addresses);
            }

            if ($updatePeopleDataFDO->getCellPhoneNumber() !== null) {
                $people->setCellPhoneNumber($updatePeopleDataFDO->getCellPhoneNumber());
            }

            if ($updatePeopleDataFDO->getHomePhoneNumber() !== null) {
                $people->setHomePhoneNumber($updatePeopleDataFDO->getHomePhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkPhoneNumber() !== null) {
                $people->setWorkPhoneNumber($updatePeopleDataFDO->getWorkPhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkFaxNumber() !== null) {
                $people->setWorkFaxNumber($updatePeopleDataFDO->getWorkFaxNumber());
            }

            if ($updatePeopleDataFDO->getObservations() !== null) {
                $people->setObservations($updatePeopleDataFDO->getObservations());
            }

            if ($updatePeopleDataFDO->getSensitiveObservations() !== null && $this->isGranted('ROLE_GESTION_SENSIBLE')) {
                $people->setSensitiveObservations($updatePeopleDataFDO->getSensitiveObservations());
            }

            if ($updatePeopleDataFDO->getEmailAddress() !== null) {
                $people->setEmailAddress($updatePeopleDataFDO->getEmailAddress());
            }

            if ($updatePeopleDataFDO->getIsReceivingNewsletter() !== null) {
                $people->setIsReceivingNewsletter($updatePeopleDataFDO->getIsReceivingNewsletter());
            }

            if ($updatePeopleDataFDO->getNewsletterDematerialization() !== null) {
                $people->setNewsletterDematerialization($updatePeopleDataFDO->getNewsletterDematerialization());
            }


            // if the connected user does not have the access to the sensible
            // inputs, we need to keep the old data instead of emptying it
            /* if (!$this->isGranted('ROLE_GESTION_SENSIBLE'))
              {
              $people->setSensitiveObservations(
              $updatePeopleDataFDO->getSensitiveObservations()
              );
              } */

            $entityManager->persist($people);
            $entityManager->flush();

            $this->addFlash(
                'success', $translator->trans('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('people_edit', ['id' => $people->getId()]);
        }

        return $this->render('People/edit.html.twig', array(
                'people' => $people,
                'people_edit' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Show the form that allows to generate and download a PDF file containing all the receipts for a given year.
     *
     * @return views
     * @param Request $request The request.
     * @param People $people The people for which we want the file
     * @Route("/{id}/generate/from-year", name="people_generate_receipt_by_year", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function generateReceiptsByYearAction(Request $request, People $people, ReceiptService $receiptService)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Find fiscal years for which there is receipts to generate
        $availableYears = $em->getRepository(Receipt::class)->findAvailableYearsByPeople($people);

        // Creating an empty FDO
        $generateTaxReceiptFromYearFDO = new GenerateTaxReceiptFromYearFDO();

        // From creation
        $generateFromYearForm = $this->createForm(
            GenerateTaxReceiptFromYearType::class,
            $generateTaxReceiptFromYearFDO,
            [
                'availableYears' => $availableYears,
            ]
        );

        $generateFromYearForm->handleRequest($request);

        return $this->render('People/generate-from-year.html.twig', [
            'from_year_form' => $generateFromYearForm->createView(),
            'people' => $people,
        ]);
    }

    /**
     * Deletes a People entity.
     * @return views
     * @param Request $request The request.
     * @param People $people The user to delete.
     * @Route("/{id}", name="people_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function deleteAction(Request $request, People $people, TranslatorInterface $translator) {
        $form = $this->createDeleteForm($people);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = $people->getFirstName();
            $lastname = $people->getLastName();

            $em = $this->getDoctrine()->getManager();
            $em->remove($people);
            $em->flush();

            $dataOfTranslation = $translator->trans('Les informations de');
            $hasBeenDeletedTranslation = $translator->trans('ont bien été supprimées');

            $confirmationMessage = sprintf(
                '%s <strong>%s %s</strong> %s.',
                $dataOfTranslation,
                $firstname,
                $lastname,
                $hasBeenDeletedTranslation
            );
            $this->addFlash(
                'success', $confirmationMessage
            );
        }

        return $this->redirectToRoute('people_list');
    }

    /**
     * Creates a form to delete a People entity.
     * @param People $people The user to display.
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(People $people) {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('people_delete', array('id' => $people->getId())))
                ->setMethod('DELETE')
                ->getForm();
    }
}
