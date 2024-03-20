<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PeopleTypeRepository")
 */
class PeopleType
{
    const CONTACT_CODE = 1;
    const SOCIAL_POLE_CODE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int Code of the type
     * @ORM\Column(name="code", type="integer", unique=true)
     */
    private $code;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /**
     * A boolean used to check if the information is sensible.
     *
     * @var boolean
     *
     * @ORM\Column(name="is_sensible", type="boolean")
     */
    private $isSensible;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\People", mappedBy="types", cascade={"persist"})
     *
     */
    private $peoples;

    public function getId(): ?int
    {
        return $this->id;
    }

    function getCode(): int
    {
        return $this->code;
    }

    function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    function getIsSensible(): ?bool
    {
        return $this->isSensible;
    }

    function setIsSensible(bool $isSensible): self
    {
        $this->isSensible = $isSensible;
        return $this;
    }
}
