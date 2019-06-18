<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\People;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;

/**
 * Member controller.
 *
 * @Route("member")
 */
class MemberController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route(path="/", name="member_list", methods={"GET"})
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('AppBundle:People')->findAll();

        $deleteForms = [];
        foreach ($people as $individual) {
            $deleteForm = $this->createDeleteForm($individual);
            $deleteForms[$individual->getId()] = $deleteForm->createView();
        }

        return $this->render('@App/Member/list.html.twig', array(
            'members' => $people,
            'member_deletion_forms' => $deleteForms,
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s</strong> a été créé.e', $user->getUsername())
            );

            return $this->redirectToRoute('member_show', array('id' => $user->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                    'danger', sprintf('L\'utilisateurice <strong>%s</strong> n\'a pas pu être créé.e', $user->getUsername())
            );
        }

        return $this->render('@App/Member/new.html.twig', array(
            'member' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a People entity.
     *
     * @Route("/{id}", name="member_show", methods={"GET"})
     * @Security("has_role('ROLE_GESTION') || (has_role('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showAction(People $individual)
    {
        $deleteForm = $this->createDeleteForm($individual);

        return $this->render('@App/Member/show.html.twig', array(
            'member' => $individual,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing People entity.
     *
     * @Route("/{id}/edit", name="member_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_GESTION') || (has_role('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function editAction(Request $request, People $individual, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $individual->getUser();

        $deleteForm = $this->createDeleteForm($individual);
        $editForm = $this->createForm('AppBundle\Form\UserGeneralDataType', $user);
        $editForm->handleRequest($request);
        $passwordForm = $this->createForm('AppBundle\Form\UserPasswordType', []);
        $passwordForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('member_edit', ['id' => $user->getId()]);
        }

        // Submit change of password
        if ($passwordForm->isSubmitted())
        {
            $oldPassword = $user->getPassword();
            $plainOldPassword = $passwordForm['oldPassword']->getData();
            $plainPassword = $passwordForm['plainPassword']->getData();

            // If a password is entered and the old password typed in is correct
            if ($plainPassword !== null && password_verify($plainOldPassword,$oldPassword)) {
                $password = $passwordEncoder->encodePassword($user, $plainPassword);
                $user->setPassword($password);

                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                        'success', sprintf('Le mot de passe a bien été modifié')
                );
            }

            // Error message
            if (!password_verify($plainOldPassword,$oldPassword))
            {
                $passwordForm->get('oldPassword')->addError(new FormError('L\'ancien mot de passe ne correspond pas'));
            }

        }

        return $this->render('@App/Member/edit.html.twig', array(
            'member' => $user,
            'edit_form' => $editForm->createView(),
            'password_form'=> $passwordForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="member_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_GESTION') || (has_role('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $user->getUsername();
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s</strong> a bien été supprimé.e.', $username)
            );
        }

        return $this->redirectToRoute('member_list');
    }

    /**
     * Creates a form to delete a People entity.
     *
     * @param People $individual The People entity
     *
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
