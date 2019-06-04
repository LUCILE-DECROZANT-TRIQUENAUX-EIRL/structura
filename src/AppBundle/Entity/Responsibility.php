<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsibility
 *
 * @ORM\Table(name="responsibility")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResponsibilityRepository")
 */
class Responsibility
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
     * Code of the responsibility used by Symfony to determine which role it is
     *
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * Name of the responsibility
     *
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    private $label;

    /**
     * Description of the responsibility
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     *
     */
    function __construct($id = -1, $code = NULL, $label = NULL, $description = NULL)
    {
        $this->id = $id;
        $this->code = $code;
        $this->label = $label;
        $this->description = $description;
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
     * @param string $label
     *
     * @return Responsibility
     */
    public function setLabel($label)
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

    /**
     * Get code
     *
     * @return string
     */
    function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return ResponsabXility
     */
    function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the responsibility description
     *
     * @return string
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the responsibility description
     *
     * @param string $description
     */
    function setDescription($description)
    {
        $this->description = $description;
    }

}
