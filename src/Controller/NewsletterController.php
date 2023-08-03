<?php

namespace App\Controller;

use App\Repository\PeopleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/{_locale}/newsletter", requirements={"_locale"="en|fr"})
 */
class NewsletterController extends AbstractController
{
    /**
     * @Route("/", name="newsletter_dematarialized", methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function dematarializedAction(PeopleRepository $peopleRepository): Response
    {
        return $this->render('Newsletter/dematarialized.html.twig', [
            'people' => $peopleRepository->findBy(['newsletterDematerialization' => true])
        ]);
    }
}
