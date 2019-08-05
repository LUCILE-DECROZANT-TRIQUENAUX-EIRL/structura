<?php
/**
 * Login controller
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Login controller
 */
class LoginController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     * @param Request $request The request.
     * @param AuthenticationUtils $authenticationUtils Error.
     * @param UserInterface $currentUser The user.
     * @return views
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils, UserInterface $currentUser = null)
    {
        $lastError = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        if (is_null($currentUser))
        {
            if (!is_null($lastError))
            {
                if (is_a($lastError, 'Symfony\Component\Security\Core\Exception\BadCredentialsException'))
                {
                    $this->addFlash(
                            'danger', 'Nom d\'utilisateur ou mot de passe invalide.'
                    );
                }
                else
                {
                    if($lastError->getMessage() == "An exception occurred in driver: SQLSTATE[HY000] [2002] Connection refused")
                    {
                        $this->addFlash(
                                'danger', 'Impossibilité de se connecter à la base de données.'
                        );
                    }
                    else
                    {
                        $this->addFlash(
                                'danger', $lastError->getMessage()
                        );
                    }
                }
            }

            $response = $this->render('Login/login.html.twig', []);
        }
        else
        {
            $response = $this->redirectToRoute('home');
        }


        return $response;
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction($param)
    {
        throw new \RuntimeException('This should never be called directly.');
    }

}
