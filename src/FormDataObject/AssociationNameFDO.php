<?php
namespace App\FormDataObject;

class AssociationNameFDO
{

    /**
     * @var string
     */
    private $name;

    function getName(): ?string
    {
        return $this->name;
    }

    function setName(string $name): AssociationNameFDO
    {
        $this->name = $name;
        return $this;
    }
}