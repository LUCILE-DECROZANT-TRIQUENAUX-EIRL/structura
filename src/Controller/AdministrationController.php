<?php
/**
 * Administration controller
 */
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Administration Controller
 */
class AdministrationController extends AbstractController
{
    /**
     * @return views
     * @param Request $request The request.
     * @Route("/administration", name="administration_dashboard")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function administrationAction(Request $request)
    {
        return $this->render('Administration/dashboard.html.twig', []);
    }
}
