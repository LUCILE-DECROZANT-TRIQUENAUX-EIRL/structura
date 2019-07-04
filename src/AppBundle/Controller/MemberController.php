<?php
/**
 * Member controller
 */

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
     * @return views
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
     * Creates a new person entity.
     * @return views
     * @param Request $request The request.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/new", name="member_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new People();
        $form = $this->createForm('AppBundle\Form\MemberType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s%s</strong> a été créé.e', $user->getFirstName(), $user->getLastName())
            );

            return $this->redirectToRoute('member_show', array('id' => $user->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                    'danger', sprintf('L\'utilisateurice <strong>%s</strong> n\'a pas pu être créé.e', $user->getFirstName(), $user->getLastName())
            );
        }

        return $this->render('@App/Member/new.html.twig', array(
            'member' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a People entity.
     * @return views
     * @param People $individual The user to display.
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
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/edit", name="member_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_GESTION') || (has_role('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function editAction(Request $request, People $individual, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $individual->getUser();

        $deleteForm = $this->createDeleteForm($individual);
        $editForm = $this->createForm('AppBundle\Form\MemberType', $individual);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('member_edit', ['id' => $user->getId()]);
        }

        return $this->render('@App/Member/edit.html.twig', array(
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
     * @Security("has_role('ROLE_GESTION') || (has_role('ROLE_INSCRIT_E') && (user.getId() == id))")
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
