<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @Gedmo\Translatable
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string|null
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="short_label", type="string", length=255, nullable=true)
     */
    private $shortLabel;

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

    /**
     * Set short label
     *
     * @param string $label The short version of this denomination's label.
     *
     * @return self
     */
    public function setShortLabel(?string $shortLabel): self
    {
        $this->shortLabel = $shortLabel;

        return $this;
    }

    /**
     * Get shortLabel
     *
     * @return string|null
     */
    public function getShortLabel(): ?string
    {
        return $this->shortLabel;
    }
}
