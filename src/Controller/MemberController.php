<?php

/**
 * Member controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\People;
use App\Entity\Address;
use App\Form\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use App\FormDataObject\UpdateMemberDataFDO;
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
    public function listAction() {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository(People::class)->findWithActiveMembership();

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
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/new", name="member_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator) {
        $updateMemberDataFDO = new UpdateMemberDataFDO();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(MemberType::class, $updateMemberDataFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $member = new People();

            $member->setDenomination($updateMemberDataFDO->getDenomination());
            $member->setFirstName($updateMemberDataFDO->getFirstName());
            $member->setLastName($updateMemberDataFDO->getLastName());

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

            $em->persist($address);
            $em->persist($member);
            $em->flush();

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
     * @return views
     * @param People $individual The user to display.
     * @Route("/{id}", name="member_show", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showAction(People $individual) {
        $deleteForm = $this->createDeleteForm($individual);

        return $this->render('Member/show.html.twig', array(
                'member' => $individual,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a People memberships.
     *
     * @return views
     * @param People $individual The user corresponding to the wanted memberships.
     * @Route("/{id}/memberships", name="member_memberships_show", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showMembershipsAction(People $individual) {
        return $this->render('Member/show-memberships.html.twig', [
                'member' => $individual,
        ]);
    }

    /**
     * Displays a form to edit an existing People entity.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/edit", name="member_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function editAction(
        Request $request,
        People $individual,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator
    )
    {
        $updateMemberDataFDO = UpdateMemberDataFDO::fromMember($individual);

        $entityManager = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($individual);
        $editForm = $this->createForm(MemberType::class, $updateMemberDataFDO);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Get the existing people to keep the sensible data it has if necessary
            $individual = $entityManager->getRepository(People::class)->findOneBy([
                'id' => $individual->getId(),
            ]);

            $individual->setDenomination($updateMemberDataFDO->getDenomination());
            $individual->setFirstName($updateMemberDataFDO->getFirstName());
            $individual->setLastName($updateMemberDataFDO->getLastName());

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

            return $this->redirectToRoute('member_edit', ['id' => $individual->getId()]);
        }

        return $this->render('Member/edit.html.twig', array(
                'member' => $individual,
                'member_edit' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a People entity.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to delete.
     * @Route("/{id}", name="member_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function deleteAction(Request $request, People $individual, TranslatorInterface $translator) {
        $form = $this->createDeleteForm($individual);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = $individual->getFirstName();
            $lastname = $individual->getLastName();

            $em = $this->getDoctrine()->getManager();
            $em->remove($individual);
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
