<?php
/**
 * User controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserGeneralDataType;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Service\Utils\RouteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends AbstractController
{
    /**
     * Lists all user entities.
     * @return views
     * @Route(path="/", name="user_list", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $currentUsers = $em->getRepository(User::class)->findAll();

        $deleteForms = [];
        foreach ($currentUsers as $currentUser) {
            $deleteForm = $this->createDeleteForm($currentUser);
            $deleteForms[$currentUser->getId()] = $deleteForm->createView();
        }

        return $this->render('User/list.html.twig', array(
            'users' => $currentUsers,
            'user_deletion_forms' => $deleteForms,
        ));
    }

    /**
     * Creates a new user entity.
     * @return views
     * @param Request $request The request.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @param RouteService $routeService The route of the service.
     * @Route("/new", name="user_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, RouteService $routeService)
    {
        // RouteService usage example
        $infos = $routeService->getPreviousRouteInfo();

        $currentUser = new User();
        $form = $this->createForm(UserType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($currentUser, $currentUser->getPlainPassword());
            $currentUser->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($currentUser);
            $em->flush();

            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s</strong> a été créé.e', $currentUser->getUsername())
            );

            return $this->redirectToRoute('user_show', array('id' => $currentUser->getId()));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                    'danger', sprintf('L\'utilisateurice <strong>%s</strong> n\'a pas pu être créé.e', $currentUser->getUsername())
            );
        }

        // Check the referer to diplay in the view the good breadcrumb
        if ($infos['_route'] === 'administration_dashboard')
        {
            $from = 'administration';
        }
        else
        {
            $from = 'list';
        }

        return $this->render('User/new.html.twig', array(
            'user' => $currentUser,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     * @return views
     * @param User $currentUser The user to display.
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function showAction(User $currentUser)
    {
        $deleteForm = $this->createDeleteForm($currentUser);

        return $this->render('User/show.html.twig', array(
            'user' => $currentUser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays history of a user editions
     * @return views
     * @param User $currentUser The user to display.
     * @Route("/{id}/history", name="user_history", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function historyAction(User $currentUser)
    {
        $deleteForm = $this->createDeleteForm($currentUser);

        return $this->render('User/history.html.twig', array(
            'user' => $currentUser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function editAction(Request $request, User $currentUser, UserPasswordEncoderInterface $passwordEncoder)
    {
        $deleteForm = $this->createDeleteForm($currentUser);
        $editForm = $this->createForm(UserGeneralDataType::class, $currentUser);
        $editForm->handleRequest($request);
        $passwordForm = $this->createForm(UserPasswordType::class, []);
        $passwordForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('user_edit', ['id' => $currentUser->getId()]);
        }

        // Submit change of password
        if ($passwordForm->isSubmitted())
        {
            $oldPassword = $currentUser->getPassword();
            $plainOldPassword = $passwordForm['oldPassword']->getData();
            $plainPassword = $passwordForm['plainPassword']->getData();

            // If a password is entered and the old password typed in is correct
            if ($plainPassword !== null && password_verify($plainOldPassword,$oldPassword)) {
                $password = $passwordEncoder->encodePassword($currentUser, $plainPassword);
                $currentUser->setPassword($password);

                $this->getDoctrine()->getManager()->persist($currentUser);
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

        return $this->render('User/edit.html.twig', array(
            'user' => $currentUser,
            'edit_form' => $editForm->createView(),
            'password_form' => $passwordForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to delete.
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function deleteAction(Request $request, User $currentUser)
    {
        $form = $this->createDeleteForm($currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUsername = $currentUser->getUsername();

            // Making sure there is no Fk error when deleting the user
            $people = $currentUser->getPeople();
            if (!empty($people))
            {
                $people->setUser(NULL);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($currentUser);
            $em->flush();
            $this->addFlash(
                    'success', sprintf('L\'utilisateurice <strong>%s</strong> a bien été supprimé.e.', $currentUsername)
            );
        }

        return $this->redirectToRoute('user_list');
    }

    /**
     * Creates a form to delete a user entity.
     * @return views
     * @param User $currentUser The user entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $currentUser)
    {
        return $this->createFormBuilder()
        ->setAction($this->generateUrl('user_delete', array('id' => $currentUser->getId())))
        ->setMethod('DELETE')
        ->getForm()
        ;
    }
}
