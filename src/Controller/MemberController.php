<?php

/**
 * Member controller
 */

namespace App\Controller;

use App\Entity\People;
use App\Entity\PeopleType;
use App\Entity\Address;
use App\Form\MemberType;
use App\Form\GenerateTaxReceiptFromYearType;
use App\FormDataObject\GenerateTaxReceiptFromYearFDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\FormDataObject\UpdateMemberDataFDO;
use App\Repository\PeopleRepository;
use App\Repository\PeopleTypeRepository;
use App\Repository\ReceiptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Member controller.
 *
 * @Route(path="/{_locale}/member", requirements={"_locale"="en|fr"})
 */
class MemberController extends AbstractController {

    /**
     * Lists all user entities.
     * @return views
     * @Route(path="/", name="member_list", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function list(PeopleRepository $peopleRepository) {

        $people = $peopleRepository->findWithActiveMembership();

        $deleteForms = [];
        foreach ($people as $individual) {
            $deleteForm = $this->createDeleteForm($individual);
            $deleteForms[$individual->getId()] = $deleteForm->createView();
        }

        return $this->render('Member/list.html.twig', array(
                'members' => $people,
                'member_deletion_forms' => $deleteForms,
        ));
    }

    /**
     * Creates a new person entity.
     * @return views
     * @param Request $request The request.
     * @param EntityManagerInterface $entityManager
     * @Route("/new", name="member_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function create(Request $request, TranslatorInterface $translator, EntityManagerInterface $entityManager) {
        $updateMemberDataFDO = new UpdateMemberDataFDO();

        $form = $this->createForm(MemberType::class, $updateMemberDataFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $member = new People();

            $member->setDenomination($updateMemberDataFDO->getDenomination());
            $member->setFirstName($updateMemberDataFDO->getFirstName());
            $member->setLastName($updateMemberDataFDO->getLastName());

            $type = $entityManager->getRepository(PeopleType::class)->findOneBy([
                'code' => PeopleType::CONTACT_CODE,
            ]);
            if ($updateMemberDataFDO->isContact())
            {
                $member->addType($type);
            }
            else
            {
                $member->removeType($type);
            }

            $typeSocialPole = $entityManager->getRepository(PeopleType::class)->findOneBy([
                'code' => PeopleType::SOCIAL_POLE_CODE,
            ]);
            if ($updateMemberDataFDO->needHelp())
            {
                $member->addType($typeSocialPole);
            }
            else
            {
                $member->removeType($typeSocialPole);
            }

            if ($updateMemberDataFDO->getAddresses()['__name__'] === null) {
                $address = new Address();
                $member->setAddresses([$address]);
            } else {
                $address = $updateMemberDataFDO->getAddresses()['__name__'];
                $member->setAddresses([$address]);
            }

            if ($updateMemberDataFDO->getCellPhoneNumber() !== null) {
                $member->setCellPhoneNumber($updateMemberDataFDO->getCellPhoneNumber());
            }

            if ($updateMemberDataFDO->getHomePhoneNumber() !== null) {
                $member->setHomePhoneNumber($updateMemberDataFDO->getHomePhoneNumber());
            }

            if ($updateMemberDataFDO->getWorkPhoneNumber() !== null) {
                $member->setWorkPhoneNumber($updateMemberDataFDO->getWorkPhoneNumber());
            }

            if ($updateMemberDataFDO->getWorkFaxNumber() !== null) {
                $member->setWorkFaxNumber($updateMemberDataFDO->getWorkFaxNumber());
            }

            if ($updateMemberDataFDO->getObservations() !== null) {
                $member->setObservations($updateMemberDataFDO->getObservations());
            }

            if ($updateMemberDataFDO->getSensitiveObservations() !== null && $this->isGranted('ROLE_GESTION_SENSIBLE')) {
                $member->setSensitiveObservations($updateMemberDataFDO->getSensitiveObservations());
            }

            if ($updateMemberDataFDO->getEmailAddress() !== null) {
                $member->setEmailAddress($updateMemberDataFDO->getEmailAddress());
            }

            if ($updateMemberDataFDO->getIsReceivingNewsletter() !== null) {
                $member->setIsReceivingNewsletter($updateMemberDataFDO->getIsReceivingNewsletter());
            }

            if ($updateMemberDataFDO->getNewsletterDematerialization() !== null) {
                $member->setNewsletterDematerialization($updateMemberDataFDO->getNewsletterDematerialization());
            }

            if ($updateMemberDataFDO->getFirstContactYear() !== null) {
                $member->setFirstContactYear($updateMemberDataFDO->getFirstContactYear());
            }

            $entityManager->persist($address);
            $entityManager->persist($member);
            $entityManager->flush();

            $userTranslation = $translator->trans('L\'utilisateurice');
            $hasBeenCreatedTranslation = $translator->trans('a été créé.e');

            $this->addFlash(
                'success', sprintf('%s <strong>%s %s</strong> %s',
                    $userTranslation,
                    $updateMemberDataFDO->getFirstName(),
                    $updateMemberDataFDO->getLastName(),
                    $hasBeenCreatedTranslation
                )
            );

            //var_dump($updateMemberDataFDO);

            return $this->redirectToRoute('member_show', array('id' => $member->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $userTranslation = $translator->trans('L\'utilisateurice');
            $couldntBeCreatedTranslation = $translator->trans('n\'a pas pu être créé.e');

            $this->addFlash(
                'danger', sprintf('%s <strong>%s</strong> %s',
                    $userTranslation,
                    $updateMemberDataFDO->getFirstName(),
                    $couldntBeCreatedTranslation
                )
            );
        }

        return $this->render('Member/new.html.twig', array(
                'member' => $updateMemberDataFDO,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a People entity.
     * @return Response
     * @param People $individual The user to display.
     * @Route("/{id}", name="member_show", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function show(Request $request, People $individual, ReceiptRepository $receiptRepository) {
        $deleteForm = $this->createDeleteForm($individual);

        // Find fiscal years for which there is receipts to generate
        $availableYears = $receiptRepository->findAvailableYearsByPeople($individual);

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

        return $this->render('Member/show.html.twig', [
            'member' => $individual,
            'hasActiveMembership' => $individual->hasActiveMembership(),
            'delete_form' => $deleteForm->createView(),
            'from_year_form' => $generateFromYearForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing People entity.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @Route("/{id}/edit", name="member_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function edit(
        Request $request,
        People $individual,
        TranslatorInterface $translator,
        PeopleRepository $peopleRepository,
        PeopleTypeRepository $peopleTypeRepository,
        EntityManagerInterface $entityManager,
    )
    {
        $updateMemberDataFDO = UpdateMemberDataFDO::fromMember($individual);

        $deleteForm = $this->createDeleteForm($individual);
        $editForm = $this->createForm(MemberType::class, $updateMemberDataFDO);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Get the existing people to keep the sensible data it has if necessary
            $individual = $peopleRepository->findOneBy([
                'id' => $individual->getId(),
            ]);

            $individual->setDenomination($updateMemberDataFDO->getDenomination());
            $individual->setFirstName($updateMemberDataFDO->getFirstName());
            $individual->setLastName($updateMemberDataFDO->getLastName());

            $type = $peopleTypeRepository->findOneBy([
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

            $typeSocialPole = $peopleTypeRepository->findOneBy([
                'code' => PeopleType::SOCIAL_POLE_CODE,
            ]);
            if ($updateMemberDataFDO->needHelp())
            {
                $individual->addType($typeSocialPole);
            }
            else
            {
                $individual->removeType($typeSocialPole);
            }

            if ($updateMemberDataFDO->getAddresses() === null)
            {
                $address = new Address();
                $individual->setAddresses([$address]);
            }
            else
            {
                $addresses = [];

                foreach ($updateMemberDataFDO->getAddresses() as $address)
                {
                    $entityManager->persist($address);
                    $addresses[] = $address;
                }

                $individual->setAddresses($addresses);
            }

            if ($updateMemberDataFDO->getCellPhoneNumber() !== null) {
                $individual->setCellPhoneNumber($updateMemberDataFDO->getCellPhoneNumber());
            }

            if ($updateMemberDataFDO->getHomePhoneNumber() !== null) {
                $individual->setHomePhoneNumber($updateMemberDataFDO->getHomePhoneNumber());
            }

            if ($updateMemberDataFDO->getWorkPhoneNumber() !== null) {
                $individual->setWorkPhoneNumber($updateMemberDataFDO->getWorkPhoneNumber());
            }

            if ($updateMemberDataFDO->getWorkFaxNumber() !== null) {
                $individual->setWorkFaxNumber($updateMemberDataFDO->getWorkFaxNumber());
            }

            if ($updateMemberDataFDO->getObservations() !== null) {
                $individual->setObservations($updateMemberDataFDO->getObservations());
            }

            if ($updateMemberDataFDO->getSensitiveObservations() !== null && $this->isGranted('ROLE_GESTION_SENSIBLE')) {
                $individual->setSensitiveObservations($updateMemberDataFDO->getSensitiveObservations());
            }

            if ($updateMemberDataFDO->getEmailAddress() !== null) {
                $individual->setEmailAddress($updateMemberDataFDO->getEmailAddress());
            }

            if ($updateMemberDataFDO->getIsReceivingNewsletter() !== null) {
                $individual->setIsReceivingNewsletter($updateMemberDataFDO->getIsReceivingNewsletter());
            }

            if ($updateMemberDataFDO->getNewsletterDematerialization() !== null) {
                $individual->setNewsletterDematerialization($updateMemberDataFDO->getNewsletterDematerialization());
            }

            if ($updateMemberDataFDO->getFirstContactYear() !== null) {
                $individual->setFirstContactYear($updateMemberDataFDO->getFirstContactYear());
            }

            // if the connected user does not have the access to the sensible
            // inputs, we need to keep the old data instead of emptying it
            /* if (!$this->isGranted('ROLE_GESTION_SENSIBLE'))
              {
              $individual->setSensitiveObservations(
              $updateMemberDataFDO->getSensitiveObservations()
              );
              } */

            $entityManager->persist($individual);
            $entityManager->flush();

            $this->addFlash(
                'success', $translator->trans('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('member_show', ['id' => $individual->getId()]);
        }

        return $this->render('Member/edit.html.twig', [
                'member' => $individual,
                'member_edit' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a People entity.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to delete.
     * @Route("/{id}", name="member_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function delete(Request $request, People $individual, TranslatorInterface $translator, EntityManagerInterface $entityManager) {
        $form = $this->createDeleteForm($individual);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = $individual->getFirstName();
            $lastname = $individual->getLastName();

            $entityManager->remove($individual);
            $entityManager->flush();

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

        return $this->redirectToRoute('member_list');
    }

    /**
     * Creates a form to delete a People entity.
     * @param People $individual The user to display.
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(People $individual) {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('member_delete', array('id' => $individual->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

}
