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
use App\Form\PeoplePersonalType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use App\FormDataObject\UpdateUserGeneralDataFDO;

/**
 * People controller.
 *
 * @Route("profile")
 */
class ProfileController extends AbstractController
{
    /**
     * Finds and displays a the connected user.
     * @return views
     * @param User $currentUser The user to display.
     * @Route("/{id}", name="profile_show", methods={"GET"})
     * @Security("(user.getId() == id)")
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
     * Displays a form to edit cpersonal infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $people The user to edit.
     * @Route("/{id}/editpersonal", name="profile_editpersonal", methods={"GET", "POST"})
     * @Security("(user.getId() == id)")
     */
    public function editPersonalAction(Request $request, User $currentUser)
    {
        if($currentUser->getPeople() != NULL)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }


        $editForm = $this->createForm(PeoplePersonalType::class, $people);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_editpersonal', ['id' => $currentUser->getId()]);
        }

        return $this->render('Profile/editpersonal.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'profile_editpersonal' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit contact infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $people The user to edit.
     * @Route("/{id}/editcontact", name="profile_editcontact", methods={"GET", "POST"})
     * @Security("(user.getId() == id)")
     */
    public function editContactAction(Request $request, User $currentUser)
    {
        if($currentUser->getPeople() != NULL)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }


        $editForm = $this->createForm(PeopleContactType::class, $people);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_editcontact', ['id' => $currentUser->getId()]);
        }

        return $this->render('Profile/editcontact.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'profile_editcontact' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit sensible infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/editsensible", name="profile_edit_profile", methods={"GET", "POST"})
     * @Security("user.getId() == id")
     */
    public function editSensibleAction(Request $request, User $currentUser, UserPasswordEncoderInterface $passwordEncoder)
    {

        $updateUserGeneralDataFDO = UpdateUserGeneralDataFDO::fromUser($currentUser);

        if($currentUser->getPeople() != NULL)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(UserGeneralDataType::class, $updateUserGeneralDataFDO);
        $editForm->handleRequest($request);
        $passwordForm = $this->createForm(UserPasswordType::class, []);
        $passwordForm->handleRequest($request);

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

            //$this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_edit_profile', ['id' => $currentUser->getId()]);
        }

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
                        'success', sprintf('Le mot de passe a bien été modifié')
                );
            }

            // Error message
            if (!password_verify($plainOldPassword,$oldPassword))
            {
                $passwordForm->get('oldPassword')->addError(new FormError('L\'ancien mot de passe ne correspond pas'));
            }
        }

        return $this->render('Profile/editsensible.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'editsensible_form' => $editForm->createView(),
            'password_form' => $passwordForm->createView(),
        ));
    }

    /**
     * Displays a form to edit roles of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $currentUser The user to edit.
     * @Route("/{id}/editroles", name="profile_editroles", methods={"GET", "POST"})
     * @Security("(user.getId() == id) && is_granted('ROLE_ADMIN')")
     */
    public function editRolesAction(Request $request, User $currentUser)
    {
        if($currentUser->getPeople() != NULL)
        {
            $people = $currentUser->getPeople();
        }
        else {
            $people = new People();
        }

        $editForm = $this->createForm(UserRolesType::class, $currentUser);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_editroles', ['id' => $currentUser->getId()]);
        }

        return $this->render('Profile/editroles.html.twig', array(
            // Returns people and user to be able to access both infos in view
            'people' => $people,
            'user' => $currentUser,
            'editroles_form' => $editForm->createView()
        ));
    }


}

?>
