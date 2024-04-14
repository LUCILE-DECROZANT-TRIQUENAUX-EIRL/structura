<?php
/**
 * Tag controller.
 * This controller is used for tags generation.
 */

namespace App\Controller;

use App\Entity\Membership;
use App\Entity\People;
use App\Form\GenerateTagType;
use App\FormDataObject\GenerateTagFDO;
use App\Message\GenerateTagMessage;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/", name="tag_index", requirements={"_locale"="en|fr"}, methods={"GET"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function index(Request $request, SessionInterface $session)
    {
        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Creating an FDO with default value
        $generateTagFDO = new GenerateTagFDO();

        // Year param override
        $year = $request->get('annee');
        if ($year) {
            $generateTagFDO->setYear(intval($year));
        }

        // Departments param override
        $departments = $request->get('departements');
        if ($departments) {
            $generateTagFDO->setDepartments($departments);
        }

        // Filtering people for the preview
        $people = $em->getRepository(People::class)->findPeopleWithAddressAndFilters([
            'year' => $generateTagFDO->getYear(),
            'departments' => $generateTagFDO->getDepartments(),
        ]);

        // Getting all years for which there is membershipsr
        // It will help setup the year filter when generation the form
        $options['availableYears'] = $em->getRepository(Membership::class)->getAvailableFiscalYears();

        // Form creation
        $generateTagForm = $this->createForm(
            GenerateTagType::class,
            $generateTagFDO,
            $options
        );

        // Tag file managment
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
            'generate_tag_form' => $generateTagForm->createView(),
        ]);
    }

    /**
     * @Route("/generate", name="tag_generate", requirements={"_locale"="en|fr"}, methods={"POST"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function generate(Request $request, MessageBusInterface $messageBus, SessionInterface $session, LoggerInterface $logger)
    {
        // Entity manager creation
        $em = $this->getDoctrine()->getManager();

        // Setting up tag filters
        $options['availableYears'] = $em->getRepository(Membership::class)->getAvailableFiscalYears();

        // Creating an empty FDO
        $generateTagFDO = new GenerateTagFDO();

        // From creation
        $generateTagForm = $this->createForm(
            GenerateTagType::class,
            $generateTagFDO,
            $options
        );

        $generateTagForm->handleRequest($request);
        if ($generateTagForm->isSubmitted() && $generateTagForm->isValid()) {
            $session->set('tagsAreGenerating', true);

            $people = $em->getRepository(People::class)->findPeopleWithAddressAndFilters([
                'year' => $generateTagFDO->getYear(),
                'departments' => $generateTagFDO->getDepartments(),
            ]);

            $tagFileFullName = $this->projectDir . '/pdf/tags.pdf';

            if (file_exists($tagFileFullName))
            {
                unlink($tagFileFullName);
            }

            // Launch generation in a job
            $messageBus->dispatch(new GenerateTagMessage($people));

            $this->addFlash('info', 'Génération du PDF en cours...');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue. La génération des étiquettes n\'a pas pû démarrer.');
        }

        $redirectUrl = $request->headers->get('referer');

        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/generation-complete", name="tag_generation_complete", requirements={"_locale"="en|fr"})
     * @Security("is_granted('ROLE_GESTION')")
     */
    public function generationComplete(Request $request, SessionInterface $session)
    {
        $session->set('tagsAreGenerating', false);
        $this->addFlash('success', 'La génération des étiquettes est terminée !');

        $redirectUrl = $request->headers->get('referer');

        return $this->redirect($redirectUrl);
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
