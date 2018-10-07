<?php
namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request, UserInterface $user = null)
    {
        if (is_null($user))
        {
            return $this->render('@App/Login/login.html.twig', []);
        }
        else
        {
            return $this->render('@App/Home/index.html.twig', []);
        }
    }
}
