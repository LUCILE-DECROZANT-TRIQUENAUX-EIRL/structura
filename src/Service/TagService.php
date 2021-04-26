<?php
/**
 * Tag Service
 */

namespace App\Service;

use Twig\Environment;
use App\Service\Utils\FileService;
use Dompdf\Dompdf;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class containing methods used to handle tags.
 */
class TagService
{
    private Environment $twig;

    private FileService $fileService;

    private string $projectDir;

    private LoggerInterface $logger;

    /**
     * Class constructor with its dependecies injections
     *
     * @param Environment $twig The templating engine used to process twig. This parameter is a dependency injection.
     * @param FileService $fileService Utility service to manipulate files. This parameter is a dependency injection.
     * @param string $projectDir The project directory url. This parameter is a dependency injection.
     * @param LoggerInterface $logger Object used for logging. This parameter is a dependency injection.
     */
    public function __construct(
        Environment $twig,
        FileService $fileService,
        string $projectDir,
        LoggerInterface $logger
    )
    {
        $this->twig = $twig;
        $this->fileService = $fileService;
        $this->projectDir = $projectDir;
        $this->logger = $logger;
    }

    /**
     * Generate a single pdf file containing all the tags corresponding to a list of given people addresses.
     * Each page can contain up to 24 tags (3 per line maximum, 8 lines maximum).
     *
     * @param array $people An array containing all the people used for tag generation.
     *
     * @return string The generated pdf full filename.
     */
    public function generateTagsPdf(array $people)
    {
        try
        {
            // File fullname, which includes the file's extension
            $fullFilename = 'tags.pdf';

            // We render the twig template of a tax receipt into pure html
            $htmlNeedingConversion = $this->twig->render('PDF/Tag/_tag_base.html.twig', [
                'people' => $people,
            ]);

            // PDF generator object instantiation
            $dompdf = new Dompdf();

            // Loading previously rendered html
            $dompdf->getOptions()->setChroot($this->projectDir . '/public');

            $dompdf->loadHtml($htmlNeedingConversion);

            // Set the page format and orientation
            $dompdf->setPaper('A4', 'portrait');

            // PDF rendering
            $dompdf->render();

            // Saving the rendering's output
            $output = $dompdf->output();

            // Saving the generated file on the server
            $fileLocation = $this->projectDir . '/pdf/' . $fullFilename;

            if (file_exists($fileLocation))
            {
                unlink($fileLocation);
            }

            $this->fileService->file_force_contents($fileLocation, $output);
        }
        catch (Exception $e)
        {
            $this->logger->critical($e->getMessage());
            $this->logger->critical($e->getTraceAsString());
            $this->logger->critical('File "' . $e->getFile() . '" & Line ' . $e->getLine());

            return null;
        }

        return $fullFilename;
    }
}
