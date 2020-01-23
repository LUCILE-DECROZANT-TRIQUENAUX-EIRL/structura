<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembershipTypeRepository")
 */
class MembershipType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $defaultAmount;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMultiMembers;

    /**
     * @ORM\Column(type="float")
     */
    private $numberMaxMembers;

    public function __construct()
    {
        $this->memberships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * Get the defaultAmount for the subscription of this MembershipType
     */
    public function getDefaultAmount() : ?float
    {
        return $this->defaultAmount;
    }

    /**
     * Set the defaultAmount for the subscription of this MembershipType
     *
     * @return self
     */
    public function setDefaultAmount(float $defaultAmount) : self
    {
        $this->defaultAmount = $defaultAmount;

        return $this;
    }

    function getIsMultiMembers(): bool
    {
        return $this->isMultiMembers;
    }

    function getNumberMaxMembers(): int
    {
        return $this->numberMaxMembers;
    }

    function setIsMultiMembers(bool $isMultiMembers): MembershipType
    {
        $this->isMultiMembers = $isMultiMembers;
        return $this;
    }

    function setNumberMaxMembers(int $numberMaxMembers): MembershipType
    {
        $this->numberMaxMembers = $numberMaxMembers;
        return $this;
    }
}