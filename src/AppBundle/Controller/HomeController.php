<?php
/**
 * Home controller
 */

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * @Security("not is_anonymous()")
     */
    public function indexAction(Request $request, UserInterface $user = null)
    {
        return $this->render('@App/Home/index.html.twig', []);
    }
}
