<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LoginController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils, UserInterface $user = null)
    {
        $lastError = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if (is_null($user))
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
                    $this->addFlash(
                            'danger', $lastError->getMessage()
                    );
                }
            }
            else
            {
                $this->addFlash(
                        'warning', sprintf('Comportement inattendu, veuillez contacter votre administrateurice.')
                );
            }
        }
        else
        {
            $this->addFlash(
                    'info', sprintf('Bienvenue %s.', $lastUsername)
            );
        }
        $response = $this->redirectToRoute('home');
        return $response;
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction($param)
    {
        $this->addFlash(
                'info', 'Vous vous êtes bien déconnecté.'
        );
        $response = $this->redirectToRoute('homepage');
    }

}
