<?php
namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class AdministrationController extends Controller
{
    /**
     * @Route("/administration", name="administration_dashboard")
     */
    public function administrationAction(Request $request, UserInterface $user = null)
    {
        // Si on n'est pas connecté, redirection vers la page de login
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
