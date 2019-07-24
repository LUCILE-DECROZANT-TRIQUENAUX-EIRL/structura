<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Denomination
 *
 * @ORM\Table(name="denomination")
 * @ORM\Entity(repositoryClass="App\Repository\DenominationRepository")
 */
class Denomination
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    private $label;

    /**
     * Class constructor
     */
    public function __construct($label)
    {
        $this->label = $label;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label Label of the denomination.
     *
     * @return Denomination
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
