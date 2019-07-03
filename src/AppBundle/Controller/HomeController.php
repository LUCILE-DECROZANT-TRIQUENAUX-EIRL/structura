<?php
/**
 * Home controller
 */

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Home controller
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @param Request $request The request.
     * @param UserInterface $user The user.
     * @return views
     */
    public function indexAction(Request $request, UserInterface $user = null)
    {
        // Si on n'est pas connecté, redirection vers la page de login
        if (is_null($user))
        {
            return $this->redirectToRoute('login');
        }
        else
        {
            return $this->render('@App/Home/index.html.twig', []);
        }
    }
}
