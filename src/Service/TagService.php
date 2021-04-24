<?php
/**
 * Tag Service
 */

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils\FileService;
use Dompdf\Dompdf;

/**
 * Class containing methods used to handle tags.
 */
class TagService
{
    /**
     * @var Environment $twig
     */
    private $twig;

    /**
     * @var FileService $fileService
     */
    private $fileService;

    /**
     * @var ParameterBagInterface $params
     */
    private $projectDir;

    /**
     * Class constructor with its dependecies injections
     *
     * @param Environment $twig The templating engine used to process twig. This parameter is a dependency injection.
     * @param FileService $fileService Utility service to manipulate files. This parameter is a dependency injection.
     * @param string $projectDir The project directory
     */
    public function __construct(
        Environment $twig,
        FileService $fileService,
        string $projectDir
    )
    {
        $this->twig = $twig;
        $this->fileService = $fileService;
        $this->projectDir = $projectDir;
    }

    /**
     * Generate a single pdf file containing all the tags corresponding to a list of given people addresses.
     * Each page can contain up to 24 tags (3 per line maximum, 8 lines maximum).
     *
     * @param array $people An array containing all the people used for tag generation.
     * @param string $filename The name of the newly generated file (without the path or the extension).
     * @param bool $isStreamed True if the file is streamed to the client, false if it generate the file on the server.
     * @param bool $isFromController True if this method is called from a controller directly.
     *
     * @return string The generated pdf full filename.
     */
    public function generateTagsPdf(
        array $people,
        string $filename = 'etiquettes',
        bool $isStreamed = false
    )
    {
        // File fullname, which includes the file's extension
        $fullFilename = $filename . '.pdf';

        // We render the twig template of a tax receipt into pure html
        $htmlNeedingConversion = $this->twig->render('PDF/Tag/_tag.html.twig', [
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

        if ($isStreamed)
        {
            $dompdf->stream($fullFilename, [
                'Attachment' => true // force download
            ]);
        }
        else
        {
            // Saving the rendering's output
            $output = $dompdf->output();

            // Saving the generated file on the server
            $fileLocation = $this->projectDir . '/pdf/' . $fullFilename;

            if (file_exists($fileLocation))
            {
                unlink($fileLocation);
            }

            $this->fileService->file_force_contents($fileLocation, $output);

            return $fullFilename;
        }
    }
}
