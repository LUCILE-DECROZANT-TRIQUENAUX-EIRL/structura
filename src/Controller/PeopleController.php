<?php
/**
 * People controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\People;
use App\Entity\Address;
use App\Form\PeopleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use App\FormDataObject\UpdatePeopleDataFDO;

/**
 * People controller.
 *
 * @Route("people")
 */
class PeopleController extends AbstractController
{
    /**
     * Lists all people entities.
     * @return views
     * @Route(path="/", name="people_list", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function listAction()
    {
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
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $updatePeopleDataFDO = new UpdatePeopleDataFDO();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PeopleType::class, $updatePeopleDataFDO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $people = new People();

            $people->setDenomination($updatePeopleDataFDO->getDenomination());
            $people->setFirstName($updatePeopleDataFDO->getFirstName());
            $people->setLastName($updatePeopleDataFDO->getLastName());

            if ($updatePeopleDataFDO->getAddresses()['__name__'] === null)
            {
                $address = new Address();
                $people->setAddresses([$address]);
            }
            else
            {
                $address = $updatePeopleDataFDO->getAddresses()['__name__'];
                $people->setAddresses([$address]);
            }

            if ($updatePeopleDataFDO->getCellPhoneNumber() !== null)
            {
                $people->setCellPhoneNumber($updatePeopleDataFDO->getCellPhoneNumber());
            }

            if ($updatePeopleDataFDO->getHomePhoneNumber() !== null)
            {
                $people->setHomePhoneNumber($updatePeopleDataFDO->getHomePhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkPhoneNumber() !== null)
            {
                $people->setWorkPhoneNumber($updatePeopleDataFDO->getWorkPhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkFaxNumber() !== null)
            {
                $people->setWorkFaxNumber($updatePeopleDataFDO->getWorkFaxNumber());
            }

            if ($updatePeopleDataFDO->getObservations() !== null)
            {
                $people->setObservations($updatePeopleDataFDO->getObservations());
            }

            if ($updatePeopleDataFDO->getSensitiveObservations() !== null && $this->isGranted('ROLE_GESTION_SENSIBLE'))
            {
                $people->setSensitiveObservations($updatePeopleDataFDO->getSensitiveObservations());
            }

            if ($updatePeopleDataFDO->getEmailAddress() !== null)
            {
                $people->setEmailAddress($updatePeopleDataFDO->getEmailAddress());
            }

            if ($updatePeopleDataFDO->getIsReceivingNewsletter() !== null)
            {
                $people->setIsReceivingNewsletter($updatePeopleDataFDO->getIsReceivingNewsletter());
            }

            if ($updatePeopleDataFDO->getNewsletterDematerialization() !== null)
            {
                $people->setNewsletterDematerialization($updatePeopleDataFDO->getNewsletterDematerialization());
            }

            $em->persist($address);
            $em->persist($people);
            $em->flush();

            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s%s</strong> a été créé.e', $updatePeopleDataFDO->getFirstName(), $updatePeopleDataFDO->getLastName())
            );

            //var_dump($updatePeopleDataFDO);

            return $this->redirectToRoute('people_show', array('id' => $people->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                    'danger', sprintf('L\'utilisateurice <strong>%s</strong> n\'a pas pu être créé.e', $updatePeopleDataFDO->getFirstName(), $updatePeopleDataFDO->getLastName())
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
    public function showAction(People $people)
    {
        $deleteForm = $this->createDeleteForm($people);

        return $this->render('People/show.html.twig', array(
            'people' => $people,
            'delete_form' => $deleteForm->createView(),
        ));
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
    public function editAction(Request $request, People $people, UserPasswordEncoderInterface $passwordEncoder)
    {
        $updatePeopleDataFDO = UpdatePeopleDataFDO::fromPeople($people);

        $entityManager = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($people);
        $editForm = $this->createForm(PeopleType::class, $updatePeopleDataFDO);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            // Get the existing people to keep the sensible data it has if necessary
            $people = $entityManager->getRepository(People::class)->findOneBy([
                'id' => $people->getId(),
            ]);

            $people->setDenomination($updatePeopleDataFDO->getDenomination());
            $people->setFirstName($updatePeopleDataFDO->getFirstName());
            $people->setLastName($updatePeopleDataFDO->getLastName());

            if ($updatePeopleDataFDO->getAddresses() === null)
            {
                $address = new Address();
                $people->setAddresses([$address]);

            }
            else
            {
                $address = $updatePeopleDataFDO->getAddresses();
                $people->setAddresses([$address]);
            }

            if ($updatePeopleDataFDO->getCellPhoneNumber() !== null)
            {
                $people->setCellPhoneNumber($updatePeopleDataFDO->getCellPhoneNumber());
            }

            if ($updatePeopleDataFDO->getHomePhoneNumber() !== null)
            {
                $people->setHomePhoneNumber($updatePeopleDataFDO->getHomePhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkPhoneNumber() !== null)
            {
                $people->setWorkPhoneNumber($updatePeopleDataFDO->getWorkPhoneNumber());
            }

            if ($updatePeopleDataFDO->getWorkFaxNumber() !== null)
            {
                $people->setWorkFaxNumber($updatePeopleDataFDO->getWorkFaxNumber());
            }

            if ($updatePeopleDataFDO->getObservations() !== null)
            {
                $people->setObservations($updatePeopleDataFDO->getObservations());
            }

            if ($updatePeopleDataFDO->getSensitiveObservations() !== null && $this->isGranted('ROLE_GESTION_SENSIBLE'))
            {
                $people->setSensitiveObservations($updatePeopleDataFDO->getSensitiveObservations());
            }

            if ($updatePeopleDataFDO->getEmailAddress() !== null)
            {
                $people->setEmailAddress($updatePeopleDataFDO->getEmailAddress());
            }

            if ($updatePeopleDataFDO->getIsReceivingNewsletter() !== null)
            {
                $people->setIsReceivingNewsletter($updatePeopleDataFDO->getIsReceivingNewsletter());
            }

            if ($updatePeopleDataFDO->getNewsletterDematerialization() !== null)
            {
                $people->setNewsletterDematerialization($updatePeopleDataFDO->getNewsletterDematerialization());
            }


            // if the connected user does not have the access to the sensible
            // inputs, we need to keep the old data instead of emptying it
            /*if (!$this->isGranted('ROLE_GESTION_SENSIBLE'))
            {
                $people->setSensitiveObservations(
                    $updatePeopleDataFDO->getSensitiveObservations()
                );
            }*/

            $entityManager->persist($address);
            $entityManager->persist($people);
            $entityManager->flush();

            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
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
     * Deletes a People entity.
     * @return views
     * @param Request $request The request.
     * @param People $people The user to delete.
     * @Route("/{id}", name="people_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_GESTION') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function deleteAction(Request $request, People $people)
    {
        $form = $this->createDeleteForm($people);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = $people->getFirstName();
            $lastname = $people->getLastName();

            $em = $this->getDoctrine()->getManager();
            $em->remove($people);
            $em->flush();
            $confirmationMessage = sprintf(
                    'Les informations de <strong>%s %s</strong> ont bien été supprimées.',
                    $firstname,
                    $lastname
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
    private function createDeleteForm(People $people)
    {
        return $this->createFormBuilder()
        ->setAction($this->generateUrl('people_delete', array('id' => $people->getId())))
        ->setMethod('DELETE')
        ->getForm();
    }
}