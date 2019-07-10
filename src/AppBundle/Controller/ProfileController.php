<?php
/**
 * Profile controller
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
 * @Route("profile")
 */
class ProfileController extends Controller
{
    /**
     * Finds and displays a the connected user.
     * @return views
     * @param People $individual The user to display.
     * @Route("/{id}", name="profile_show", methods={"GET"})
     * @Security("(individual.getId() == id)")
     */
    public function showAction(People $individual)
    {
        $user = $individual->getUser();
        return $this->render('@App/Profile/show.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $user,
        ));
    }

    /**
     * Displays a form to edit cpersonal infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @Route("/{id}/editpersonal", name="profile_editpersonal", methods={"GET", "POST"})
     * @Security("(individual.getId() == id)")
     */
    public function editPersonalAction(Request $request, People $individual)
    {
        $user = $individual->getUser();

        $editForm = $this->createForm('AppBundle\Form\MemberPersonalType', $individual);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_editpersonal', ['id' => $individual->getId()]);
        }

        return $this->render('@App/Profile/editpersonal.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $user,
            'profile_editpersonal' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit contact infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param People $individual The user to edit.
     * @Route("/{id}/editcontact", name="profile_editcontact", methods={"GET", "POST"})
     * @Security("(individual.getId() == id)")
     */
    public function editContactAction(Request $request, People $individual)
    {
        $user = $individual->getUser();

        $editForm = $this->createForm('AppBundle\Form\MemberContactType', $individual);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_editcontact', ['id' => $individual->getId()]);
        }

        return $this->render('@App/Profile/editcontact.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $user,
            'profile_editcontact' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit sensible infos of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $user The user to edit.
     * @param UserPasswordEncoderInterface $passwordEncoder Encodes the password.
     * @Route("/{id}/editsensible", name="profile_editsensible", methods={"GET", "POST"})
     * @Security("(individual.getId() == id) && has_role('ROLE_ADMIN')")
     */
    public function editSensibleAction(Request $request, People $individual, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $individual->getUser();

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

            return $this->redirectToRoute('profile_editsensible', ['id' => $individual->getId()]);
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

        return $this->render('@App/Profile/editsensible.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $user,
            'editsensible_form' => $editForm->createView(),
            'password_form' => $passwordForm->createView(),
        ));
    }

    /**
     * Displays a form to edit roles of the connected user.
     * @return views
     * @param Request $request The request.
     * @param User $user The user to edit.
     * @Route("/{id}/editroles", name="profile_editroles", methods={"GET", "POST"})
     * @Security("(individual.getId() == id) && has_role('ROLE_ADMIN')")
     */
    public function editRolesAction(Request $request, People $individual)
    {
        $user = $individual->getUser();

        $editForm = $this->createForm('AppBundle\Form\UserGeneralDataType', $user);
        $editForm->handleRequest($request);

        // Submit change of general infos
        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'success', sprintf('Les informations ont bien été modifiées')
            );

            return $this->redirectToRoute('profile_editsensible', ['id' => $individual->getId()]);
        }

        return $this->render('@App/Profile/editroles.html.twig', array(
            // Returns member and user to be able to access both infos in view
            'member' => $individual,
            'user' => $user,
            'editroles_form' => $editForm->createView()
        ));
    }


}

?>
