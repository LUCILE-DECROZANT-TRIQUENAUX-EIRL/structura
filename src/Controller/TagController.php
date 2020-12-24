<?php
/**
 * Tag controller.
 * This controller is used for tags generation.
 */

namespace App\Controller;

use App\Entity\People;
use App\Service\TagService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/{_locale}/tag", requirements={"_locale"="en|fr"})
 */
class TagController extends AbstractController
{
    /**
     * @Route("/", name="tag_index", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function index()
    {
        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Getting all people sorted by last name
        $people = $em->getRepository(People::class)->findBy([], ['lastName' => 'ASC']);

        // Returning the rendered view
        return $this->render('Tag/index.html.twig', [
            'people' => $people,
        ]);
    }

    /**
     * @Route("/generate", name="tag_generate", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function generate(TagService $tagService)
    {
        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Getting all people sorted by last name
        $people = $em->getRepository(People::class)->findBy([], ['lastName' => 'ASC']);

        // Generating the pdf file containing tags and returning it.
        return $tagService->generateTagsPdf($people, 'etiquettes', true, true);
    }
}
