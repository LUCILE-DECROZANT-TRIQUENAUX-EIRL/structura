<?php
/**
 * User controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Responsibility;
use App\Exception\FormIsInvalid;
use App\Form\UserGeneralDataType;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\FormDataObject\UpdateUserGeneralDataFDO;
use App\Service\Utils\RouteService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * User controller.
 *
 * @Route(path="/{_locale}/user", requirements={"_locale"="en|fr"})
 */
class UserController extends AbstractController
{
    public $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

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
     * @param string $from Previous page.
     * @Route("/new/{from}", name="user_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createFromListAction(Request $request, UserService $userService, string $from)
    {
        // Generate the form with a prefilled user in it
        $createdUser = new User();
        $form = $this->createForm(UserType::class, $createdUser);
        $form->handleRequest($request);

        // If the form is submited, create and save the user
        if ($form->isSubmitted())
        {
            try
            {
                $createdUser = $userService->createUser($createdUser, $form);
            }
            catch (FormIsNotSubmitted $ex)
            {
                $userTranslation = $this->translator->trans('L\'utilisateurice');
                $couldntBeCreatedTranslation = $this->translator->trans('n\'a pas pu être créé.e');

                $this->addFlash(
                    'danger', sprintf('%s <strong>%s</strong> %s',
                        $userTranslation,
                        $createdUser->getUsername(),
                        $couldntBeCreatedTranslation
                    )
                );

                return $this->renderNewUserView($from, $form, $createdUser);
            }
            catch (FormIsInvalid $ex)
            {
                $userTranslation = $this->translator->trans('L\'utilisateurice');
                $couldntBeCreatedTranslation = $this->translator->trans('n\'a pas pu être créé.e');

                $this->addFlash(
                    'danger', sprintf('%s <strong>%s</strong> %s',
                        $userTranslation,
                        $createdUser->getUsername(),
                        $couldntBeCreatedTranslation
                    )
                );

                return $this->renderNewUserView($from, $form, $createdUser);
            }
            catch (\Exception $e)
            {
                $this->addFlash(
                        'danger',
                        $this->translator->trans('Une erreur est survenue, veuillez réessayer plus tard.')
                );
                return $this->renderNewUserView($from, $form, $createdUser);
            }

            $userTranslation = $this->translator->trans('L\'utilisateurice');
            $hasBeenCreatedTranslation = $this->translator->trans('a été créé.e');

            $this->addFlash(
                'success', sprintf('%s <strong>%s</strong> %s',
                    $userTranslation,
                    $createdUser->getUsername(),
                    $hasBeenCreatedTranslation
                )
            );

            // User creation being successfull, generate an empty form
            $createdUser = new User();
            $form = $this->createForm(UserType::class, $createdUser);
            return $this->renderNewUserView($from, $form);
        }
        return $this->renderNewUserView($from, $form);
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
     * @param UserPasswordHasherInterface $passwordHasher Encodes the password.
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') || (is_granted('ROLE_INSCRIT_E') && (user.getId() == id))")
     */
    public function editAction(Request $request, User $currentUser, UserPasswordHasherInterface $passwordHasher)
    {
        $updateUserGeneralDataFDO = UpdateUserGeneralDataFDO::fromUser($currentUser);

        $entityManager = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($currentUser);
        $editForm = $this->createForm(UserGeneralDataType::class, $updateUserGeneralDataFDO);
        $editForm->handleRequest($request);
        $passwordForm = $this->createForm(UserPasswordType::class, []);
        $passwordForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            // Clear the entity manager cache to get fresh data from the database
            $entityManager->detach($currentUser);

            // Get the existing user to keep the automatic responsibilities it has
            /** @var User $currentUser */
            $currentUser = $entityManager->getRepository(User::class)->findOneBy([
                'id' => $currentUser->getId(),
            ]);

            // We save form data in the entity manager cached user
            $currentUser->setUsername($updateUserGeneralDataFDO->getUsername());

            // Same idea with responsibilities
            $responsibilitiesToSave = $updateUserGeneralDataFDO->getResponsibilities()->toArray();

            // Keep the automatic responsibilities for this user
            $oldResponsibilities = $currentUser->getResponsibilities();

            foreach ($oldResponsibilities as $oldResponsibility)
            {
                if ($oldResponsibility->isAutomatic())
                {
                    $responsibilitiesToSave[] = $oldResponsibility;
                }
            }

            // Once we have old responsibilities and form responsibilities, we save them
            $currentUser->setResponsibilities($responsibilitiesToSave);

            // by default, add the registered responsibility
            $registeredResponsibility = $entityManager->getRepository(Responsibility::class)->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);

            $currentUser->addResponsibility($registeredResponsibility);

            // remove responsibilities that conflict themselves
            $sympathizeResponsibility = $entityManager->getRepository(Responsibility::class)->findOneBy([
                'label' => Responsibility::SYMPATHIZE_LABEL,
            ]);
            $memberResponsibility = $entityManager->getRepository(Responsibility::class)->findOneBy([
                'label' => Responsibility::MEMBER,
            ]);
            $exMemberResponsibility = $entityManager->getRepository(Responsibility::class)->findOneBy([
                'label' => Responsibility::EX_MEMBER,
            ]);
            if ($currentUser->hasResponsibility($sympathizeResponsibility))
            {
                $currentUser->removeResponsibility($memberResponsibility);
            }
            else if ($currentUser->hasResponsibility($memberResponsibility))
            {
                $currentUser->removeResponsibility($exMemberResponsibility);
            }

            $entityManager->persist($currentUser);
            $entityManager->flush();

            $this->addFlash(
                    'success', $this->translator->trans('Les informations ont bien été modifiées')
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
                $password = $passwordHasher->hashPassword($currentUser, $plainPassword);
                $currentUser->setPassword($password);

                $this->getDoctrine()->getManager()->persist($currentUser);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                        'success', $this->translator->trans('Le mot de passe a bien été modifié')
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

            $userTranslation = $this->translator->trans('L\'utilisateurice');
            $hasBeenDeletedTranslation = $this->translator->trans('ont bien été supprimées');

            $confirmationMessage = sprintf(
                '%s <strong>%s %s</strong> %s.',
                $userTranslation,
                $currentUsername,
                $hasBeenDeletedTranslation
            );
            $this->addFlash(
                'success', $confirmationMessage
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

    /**
     * Render the good template depending on the $from variable
     * @param string $from name of the previous page
     * @param Form $form user creation form
     * @param User|null $createdUser the created user (default: null)
     * @return render
     * @throws NotFoundHttpException If $from does not make part of the managed list
     */
    private function renderNewUserView($from, $form, $createdUser = null)
    {
        if (!empty($createdUser))
        {
            $formParameters = [
                'form' => $form->createView(),
                'user' => $createdUser
            ];
        }
        else
        {
            $formParameters = [
                'form' => $form->createView(),
            ];
        }
        switch ($from)
        {
            case 'dashboard':
                return $this->render('User/new_from_dashboard.html.twig', $formParameters);
                break;

            case 'list':
                return $this->render('User/new_from_list.html.twig', $formParameters);
                break;

            default:
                throw new NotFoundHttpException($this->translator->trans('La page demandée n\'existe pas'));
                break;
        }
    }
}
