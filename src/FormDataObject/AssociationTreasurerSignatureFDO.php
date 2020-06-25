<?php
namespace App\FormDataObject;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssociationTreasurerSignatureFDO
{

    /**
     * @var UploadedFile
     */
    private $treasurerSignature;

    function getTreasurerSignature(): ?UploadedFile
    {
        return $this->treasurerSignature;
    }

    function setTreasurerSignature(UploadedFile $treasurerSignature): AssociationTreasurerSignatureFDO
    {
        $this->treasurerSignature = $treasurerSignature;
        return $this;
    }
}