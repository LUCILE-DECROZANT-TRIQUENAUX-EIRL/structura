<?php
/**
 * Profile controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\People;
use App\Form\UserGeneralDataType;
use App\Form\UserPasswordType;
use App\Form\MemberContactType;
use App\Form\MemberPersonalType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;

/**
 * Member controller.
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
            $individual = $currentUser->getPeople();
        }
        else {
            $individual = new People();
        }

        return $this->render('Profile/show.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $currentUser,
        ));
    }

    /**
     * Displays a form to edit cpersonal infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @Route("/{id}/editpersonal", name="profile_editpersonal", methods={"GET", "POST"})
     * @Security("(user.getId() == id)")
     */
    public function editPersonalAction(Request $request, User $currentUser)
    {
        if($currentUser->getPeople() != NULL)
        {
            $individual = $currentUser->getPeople();
        }
        else {
            $individual = new People();
        }


        $editForm = $this->createForm(MemberPersonalType::class, $individual);
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
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $currentUser,
            'profile_editpersonal' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit contact infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @Route("/{id}/editcontact", name="profile_editcontact", methods={"GET", "POST"})
     * @Security("(user.getId() == id)")
     */
    public function editContactAction(Request $request, User $currentUser)
    {
        if($currentUser->getPeople() != NULL)
        {
            $individual = $currentUser->getPeople();
        }
        else {
            $individual = new People();
        }


        $editForm = $this->createForm(MemberContactType::class, $individual);
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
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
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
     * @Route("/{id}/editsensible", name="profile_editsensible", methods={"GET", "POST"})
     * @Security("user.getId() == id")
     */
    public function editSensibleAction(Request $request, User $currentUser, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($currentUser->getPeople() != NULL)
        {
            $individual = $currentUser->getPeople();
        }
        else {
            $individual = new People();
        }


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

            return $this->redirectToRoute('profile_editsensible', ['id' => $currentUser->getId()]);
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

        return $this->render('Profile/editsensible.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
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
            $individual = $currentUser->getPeople();
        }
        else {
            $individual = new People();
        }

        $editForm = $this->createForm(UserGeneralDataType::class, $currentUser);
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
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $currentUser,
            'editroles_form' => $editForm->createView()
        ));
    }


}

?>