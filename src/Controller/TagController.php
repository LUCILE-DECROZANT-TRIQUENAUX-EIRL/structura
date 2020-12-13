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
        // Entity manager
        $em = $this->getDoctrine()->getManager();
        $people = $em->getRepository(People::class)->findBy([], ['lastName' => 'ASC']);

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
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // $people = $em->getRepository(People::class)->findAll();
        $people = $em->getRepository(People::class)->findBy([], ['lastName' => 'ASC']);

        return $tagService->generateTagsPdf($people, 'etiquettes', true, true);
    }
}
