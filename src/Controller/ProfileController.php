<?php
/**
 * Profile controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\People;
use App\Form\UserGeneralDataType;
use App\Form\UserPasswordType;
use App\Form\UserRolesType;
use App\Form\PeopleContactType;
use App\Form\PeoplePersonalDataType;
use App\Form\PeopleNewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use App\FormDataObject\UpdateUserGeneralDataFDO;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * People controller.
 *
 * @Route(path="/{_locale}/profile", requirements={"_locale"="en|fr"})
 */
class ProfileController extends AbstractController
{
    /**
     * Finds and displays a the connected user.
     * @return views
     * @param User $currentUser The user to display.
     * @Route("/{id}", name="profile_show", methods={"GET"})
     * @Security("not is_anonymous() && (user.getId() == id)")
     */
    public function showAction(User $currentUser)
    {
        if($currentUser->getPeople() != NULL)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }

        return $this->render('Profile/show.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
        ));
    }

    /**
     * Displays a form to edit personal infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $people The user to edit.
     * @Route("/{id}/editpersonal", name="profile_edit_personal_data", methods={"GET", "POST"})
     * @Security("not is_anonymous() && (user.getId() == id)")
     */
    public function editPersonalAction(Request $request, User $currentUser, TranslatorInterface $translator)
    {
        if($currentUser->getPeople() != NULL)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }

        $editForm = $this->createForm(PeoplePersonalDataType::class, $people);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', $translator->trans('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_edit_personal_data', ['id' => $currentUser->getId()]);
        }

        return $this->render('Profile/editpersonal.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'profile_edit_personal_data' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit pseudonym of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to edit.
     * @Route("/{id}/editpseudonym", name="profile_edit_pseudonym", methods={"GET", "POST"})
     * @Security("not is_anonymous() && user.getId() == id")
     */
    public function editPseudonymAction(Request $request, User $currentUser, TranslatorInterface $translator)
    {
        $updateUserGeneralDataFDO = UpdateUserGeneralDataFDO::fromUser($currentUser);

        if ($currentUser->getPeople() != null)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(UserGeneralDataType::class, $updateUserGeneralDataFDO);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            // Get the existing user to keep the automatic responsibilities it has
            /** @var User $currentUser */
            $currentUser = $entityManager->getRepository(User::class)->findOneBy([
                'id' => $currentUser->getId(),
            ]);

            // We save form data in the entity manager cached user
            $currentUser->setUsername($updateUserGeneralDataFDO->getUsername());

            $entityManager->persist($currentUser);
            $entityManager->flush();

            $this->addFlash(
                    'success', $translator->trans('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_edit_pseudonym', ['id' => $currentUser->getId()]);
        }

        return $this->render('Profile/edit-pseudonym.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit password of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/editpassword", name="profile_edit_password", methods={"GET", "POST"})
     * @Security("not is_anonymous() && user.getId() == id")
     */
    public function editProfileAction(Request $request, User $currentUser, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        if ($currentUser->getPeople() != null)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $passwordForm = $this->createForm(UserPasswordType::class, []);
        $passwordForm->handleRequest($request);

        // Submit change of password
        if ($passwordForm->isSubmitted())
        {
            // Get the existing user to keep the automatic responsibilities it has
            /** @var User $currentUser */
            $currentUser = $entityManager->getRepository(User::class)->findOneBy([
                'id' => $currentUser->getId(),
            ]);

            $oldPassword = $currentUser->getPassword();
            $plainOldPassword = $passwordForm['oldPassword']->getData();
            $plainPassword = $passwordForm['plainPassword']->getData();

            // If a password is entered and the old password typed in is correct
            if ($plainPassword !== null && password_verify($plainOldPassword,$oldPassword)) {
                $password = $passwordEncoder->encodePassword($currentUser, $plainPassword);
                $currentUser->setPassword($password);

                $entityManager->persist($currentUser);
                $entityManager->flush();

                $this->addFlash(
                        'success', $translator->trans('Le mot de passe a bien été modifié')
                );
            }

            // Error message
            if (!password_verify($plainOldPassword,$oldPassword))
            {
                $passwordForm->get('oldPassword')->addError(new FormError('L\'ancien mot de passe ne correspond pas'));
            }
        }

        return $this->render('Profile/edit-password.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'password_form' => $passwordForm->createView(),
        ));
    }

    /**
     * Displays a form to edit newsletter parameters of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to edit.
     * @Route("/{id}/editnewsletter", name="profile_edit_newsletter", methods={"GET", "POST"})
     * @Security("not is_anonymous() && user.getId() == id")
     */
    public function editNewsletterSubscriptionAction(Request $request, User $currentUser, TranslatorInterface $translator)
    {
        if ($currentUser->getPeople() != null)
        {
            $people = $currentUser->getPeople();
        }
        else
        {
            $people = new People();
        }

        $editForm = $this->createForm(PeopleNewsletterType::class, $people);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', $translator->trans('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_edit_newsletter', ['id' => $currentUser->getId()]);
        }

        return $this->render('Profile/edit-newsletter.html.twig', [
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'form' => $editForm->createView(),
        ]);
    }
}
