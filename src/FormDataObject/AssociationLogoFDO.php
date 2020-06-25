<?php
namespace App\FormDataObject;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssociationLogoFDO
{

    /**
     * @var UploadedFile
     */
    private $logo;

    function getLogo(): ?UploadedFile
    {
        return $this->logo;
    }

    function setLogo(UploadedFile $logo): AssociationLogoFDO
    {
        $this->logo = $logo;
        return $this;
    }
}