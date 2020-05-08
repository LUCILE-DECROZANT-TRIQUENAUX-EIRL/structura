<?php

namespace App\Service\Utils;

/**
 * Class containing methods used to manipulate files.
 */
class FileService
{
    /**
     * Has the same behavior as the file_put_contents function but it
     * also creates the directories in which the file will be saved.
     *
     * @param string $fullPath The file path (including the file's directories)
     * @param mixed $contents The contents you want to put in the file.
     * @param mixed $flags See file_put_contents flags.
     */
    function file_force_contents($fullPath, $contents, $flags = 0)
    {
        // Putting all directories names in an array
        $parts = explode('/', $fullPath);

        // Saving file name
        $filename = array_pop($parts);

        // Saving directories path
        $dir = implode( '/', $parts );

        // If the directories don't exists
        if (!is_dir($dir))
        {
            // Creates it
            mkdir($dir, 0774, true);
        }

        file_put_contents($fullPath, $contents, $flags);
    }
}