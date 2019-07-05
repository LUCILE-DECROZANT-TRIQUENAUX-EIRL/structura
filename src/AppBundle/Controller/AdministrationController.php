<?php
/**
 * Administration controller
 */
namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/administration", name="administration_dashboard")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function administrationAction(Request $request)
    {
        return $this->render('@App/Administration/dashboard.html.twig', []);
    }
}
