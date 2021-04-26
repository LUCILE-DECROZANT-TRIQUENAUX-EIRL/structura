<?php

namespace App\Controller\Ajax;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/{_locale}/ajax/tag", requirements={"_locale"="en|fr"})
 */
class TagAjaxController extends AbstractFOSRestController
{
    /**
     * @return Response
     *
     * @Route("/check-generation-tag-pdf", name="check_generation_tag_pdf", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function checkGenerationTagPdfAction(string $projectDir)
    {
        $tagFileFullName = $projectDir . '/pdf/tags.pdf';
        $tagFileExists = file_exists($tagFileFullName);

        $response = new Response();
        $response->setContent(json_encode([
            'isGenerationComplete' => $tagFileExists,
        ]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
