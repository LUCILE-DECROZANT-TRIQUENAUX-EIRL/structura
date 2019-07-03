<?php
/**
 * Administration controller
 */
namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Administration Controller
 */
class AdministrationController extends Controller
{
    /**
     * @return views
     * @param Request $request The request.
     * @param UserInterface $user The user.
     * @Route("/administration", name="administration_dashboard")
     */
    public function administrationAction(Request $request, UserInterface $user = null)
    {
        // If not connecter, redirects to the login page
        if (is_null($user))
        {
            return $this->redirectToRoute('login');
        }
        else
        {
            return $this->render('@App/Administration/dashboard.html.twig', []);
        }
    }
}
