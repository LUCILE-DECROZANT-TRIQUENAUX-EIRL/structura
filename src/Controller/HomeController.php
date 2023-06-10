<?php
/**
 * Home controller
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\People;
use App\Repository\PeopleRepository;

/**
 * Home controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/")
     *
     * Redirect to the home route to handle translations
     */
    public function redirectAction()
    {
        return $this->redirectToRoute('home', [], 301);
    }

    /**
     * @Route("/{_locale}/", name="home", requirements={"_locale"="en|fr"})
     * @param Request $request The request.
     * @param UserInterface $user The user.
     * @return views
     * @Security("not is_anonymous()")
     */
    public function indexAction(Request $request, PeopleRepository $peopleRepository)
    {
        $people = $peopleRepository->findWithOutdatedMembership();

        return $this->render('Home/index.html.twig', array(
            'people' => $people
        ));
    }
}
