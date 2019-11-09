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

/**
 * Home controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request The request.
     * @param UserInterface $user The user.
     * @return views
     * @Security("not is_anonymous()")
     */
    public function indexAction(Request $request/*, UserInterface $currentuser = null*/)
    {
        $em = $this->getDoctrine()->getManager();

        $peoplesWithoutActiveMembership = $em->getRepository(People::class)
            ->findWithNoActiveMembership();

        $peoplesWithoutMembership = $em->getRepository(People::class)
            ->findWithNoMembership();

        $peoples = \array_udiff(
            $peoplesWithoutActiveMembership,
            $peoplesWithoutMembership,
            function($peopleA, $peopleB) {
                return $peopleA->getId() - $peopleB->getId();
            }
        );

        return $this->render('Home/index.html.twig', array(
            'peoples' => $peoples
        ));
    }
}
