<?php

namespace App\Controller\Ajax;

use App\Entity\People;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @Route("/ajax/people", name="ajax_people_")
 */
class PeopleAjaxController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/{id}",
     *     name = "show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPeopleAction(People $people)
    {
        return $this->handleView($this->view($people));
    }

    /**
     * Finds and displays the recap of a people.
     * @return views
     * @param People $people The user we want the recap
     * @Route("/{id}/recap", name="recap", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPeopleRecapAction(People $people)
    {
        $response = $this->render('Membership/_people-recap.html.twig', array(
            'people' => $people,
        ));

        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Finds and displays the placeholder for a people.
     * @return views
     * @Route("/placeholder", name="placeholder", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function getPeoplePlaceholderAction()
    {
        $response = $this->render('Membership/_people-placeholder.html.twig');

        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}