<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsability
 *
 * @ORM\Table(name="responsability")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResponsabilityRepository")
 */
class Responsability
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
     * Code of the responsability used by Symfony to determine which role it is
     *
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * Name of the responsability
     *
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    private $label;

    /**
     * Description of the responsability
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

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
     * @return Responsability
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
     * Get the responsability description
     *
     * @return string
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the responsability description
     *
     * @param string $description
     */
    function setDescription($description)
    {
        $this->description = $description;
    }

}

