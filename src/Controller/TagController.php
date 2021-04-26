<?php
/**
 * Tag controller.
 * This controller is used for tags generation.
 */

namespace App\Controller;

use App\Entity\People;
use App\Message\GenerateTagMessage;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/{_locale}/tag", requirements={"_locale"="en|fr"})
 */
class TagController extends AbstractController
{
    private string $projectDir;

    /**
     * Class constructor with its dependencies injections
     *
     * @param string $projectDir The project directory
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @Route("/", name="tag_index", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function index(SessionInterface $session)
    {
        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Getting all people sorted by last name
        $people = $em->getRepository(People::class)->findBy([], ['lastName' => 'ASC']);

        $tagFileFullName = $this->projectDir . '/pdf/tags.pdf';
        $fileExists = file_exists($tagFileFullName);

        if ($fileExists)
        {
            $session->set('tagsAreGenerating', false);
            $tagsAreGenerating = false;
        }
        else
        {
            $tagsAreGenerating = $session->get('tagsAreGenerating', false);

            if (!$tagsAreGenerating)
            {
                $this->addFlash('info', 'Générez les étiquettes pour pouvoir télécharger le PDF.');
            }
        }

        // Returning the rendered view
        return $this->render('Tag/index.html.twig', [
            'people' => $people,
            'fileExists' => $fileExists,
            'tagsAreGenerating' => $tagsAreGenerating,
        ]);
    }

    /**
     * @Route("/generate", name="tag_generate", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function generate(MessageBusInterface $messageBus, SessionInterface $session, LoggerInterface $logger)
    {
        $session->set('tagsAreGenerating', true);

        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Getting all people sorted by last name
        $people = $em->getRepository(People::class)->findPeopleWithAddress();

        $tagFileFullName = $this->projectDir . '/pdf/tags.pdf';

        if (file_exists($tagFileFullName))
        {
            unlink($tagFileFullName);
        }

        // Launch generation in a job
        $messageBus->dispatch(new GenerateTagMessage($people));

        $this->addFlash('info', 'Génération du PDF en cours...');

        return $this->redirectToRoute('tag_index');
    }

    /**
     * @Route("/generation-complete", name="tag_generation_complete", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function generationComplete(SessionInterface $session)
    {
        $session->set('tagsAreGenerating', false);
        $this->addFlash('success', 'La génération des étiquettes est terminée !');

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
        $newFilename = 'etiquettes.pdf';
        $tagFileFullName = $this->projectDir . '/pdf/tags.pdf';
        $fileExists = file_exists($tagFileFullName);

        if (!$fileExists)
        {
            $this->addFlash('danger', 'Le fichier des étiquettes ne peut pas être téléchargé.');

            return $this->redirectToRoute('tag_index');
        }

        $response = new BinaryFileResponse($tagFileFullName);

        // Set mime type of the file to pdf
        $response->headers->set('Content-Type', 'application/pdf');

        // Will make the browser directly download and not try to open the pdf
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $newFilename
        );

        return $response;
    }
}
