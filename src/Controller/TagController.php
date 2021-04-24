<?php
/**
 * Tag controller.
 * This controller is used for tags generation.
 */

namespace App\Controller;

use App\Entity\People;
use App\Message\GenerateTagMessage;
use App\Service\TagService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
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
    public function generate(MessageBusInterface $messageBus)
    {
        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Getting all people sorted by last name
        $people = $em->getRepository(People::class)->findBy([], ['lastName' => 'ASC']);

        // Launch generation in a job
        $messageBus->dispatch(new GenerateTagMessage($people));

        return $this->redirectToRoute('tag_index');
    }

    /**
     * @return BinaryFileResponse
     * @param Request $request The request.
     * @Route("/download-tag-pdf", name="download_tag_pdf", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function downloadTagPdf()
    {
        $pdfFolderPath = $this->getParameter('kernel.project_dir') . '/pdf/';
        $filename = 'etiquettes.pdf';

        $response = new BinaryFileResponse($pdfFolderPath . $filename);

        // Set mime type of the file to pdf
        $response->headers->set('Content-Type', 'application/pdf');

        // Will make the browser directly download and not try to open the pdf
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
