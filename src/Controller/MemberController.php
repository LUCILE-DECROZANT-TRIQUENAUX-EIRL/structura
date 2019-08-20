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

/**
 * Member controller.
 *
 * @Route("member")
 */
class MemberController extends AbstractController
{
    /**
     * Lists all user entities.
     * @return views
     * @Route(path="/", name="member_list", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository(People::class)->findAll();

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
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $currentUser = new People();
        $form = $this->createForm(MemberType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            if ($currentUser->getAddresses()['__name__'] === null)
            {
                $address = new Address();
                $currentUser->setAddresses([$address]);
            }
            else
            {
                $address = $currentUser->getAddresses()['__name__'];
                $currentUser->setAddresses([$address]);
            }
            $em->persist($address);
            $em->persist($currentUser);
            $em->flush();

            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s%s</strong> a été créé.e', $currentUser->getFirstName(), $currentUser->getLastName())
            );

            return $this->redirectToRoute('member_show', array('id' => $currentUser->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                    'danger', sprintf('L\'utilisateurice <strong>%s</strong> n\'a pas pu être créé.e', $currentUser->getFirstName(), $currentUser->getLastName())
            );
        }

        return $this->render('Member/new.html.twig', array(
            'member' => $currentUser,
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
    public function showAction(People $individual)
    {
        $deleteForm = $this->createDeleteForm($individual);

        return $this->render('Member/show.html.twig', array(
            'member' => $individual,
            'delete_form' => $deleteForm->createView(),
        ));
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
    public function editAction(Request $request, People $individual, UserPasswordEncoderInterface $passwordEncoder)
    {
        $updateMemberDataFDO = UpdateMemberDataFDO::fromMember($individual);

        $entityManager = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($individual);
        $editForm = $this->createForm(MemberType::class, $updateMemberDataFDO);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            //$entityManager = $this->getDoctrine()->getManager();
            // Clear the entity manager cache to get fresh data from the database
            //$entityManager->clear();
            //$entityManager->detach($individual);

            // Get the existing people to keep the sensible data it has if necessary
            $individual = $entityManager->getRepository(People::class)->findOneBy([
                'id' => $individual->getId(),
            ]);

            // if the connected user does not have the access to the sensible
            // inputs, we need to keep the old data instead of emptying it
            if (!$this->isGranted('ROLE_GESTION_SENSIBLE'))
            {
                $individual->setSensitiveObservations(
                    $updateMemberDataFDO->getSensitiveObservations()
                );
            }

            /*$individual->setFirstName($updateMemberDataFDO->getFirstName());
            $individual->setLastName($updateMemberDataFDO->getLastName());*/

            $entityManager->persist($individual);
            $entityManager->flush();

            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
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
    public function deleteAction(Request $request, People $individual)
    {
        $form = $this->createDeleteForm($individual);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = $individual->getFirstName();
            $lastname = $individual->getLastName();

            $em = $this->getDoctrine()->getManager();
            $em->remove($individual);
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

        return $this->redirectToRoute('member_list');
    }

    /**
     * Creates a form to delete a People entity.
     * @param People $individual The user to display.
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(People $individual)
    {
        return $this->createFormBuilder()
        ->setAction($this->generateUrl('member_delete', array('id' => $individual->getId())))
        ->setMethod('DELETE')
        ->getForm()
        ;
    }
}
