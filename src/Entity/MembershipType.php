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
     * @ORM\OneToMany(targetEntity="App\Entity\Membership", mappedBy="type")
     */
    private $memberships;

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

    /**
     * @return Collection|Membership[]
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Membership $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships[] = $membership;
            $membership->setType($this);
        }

        return $this;
    }

    public function removeMembership(Membership $membership): self
    {
        if ($this->memberships->contains($membership)) {
            $this->memberships->removeElement($membership);
            // set the owning side to null (unless already changed)
            if ($membership->getType() === $this) {
                $membership->setType(null);
            }
        }

        return $this;
    }
}
