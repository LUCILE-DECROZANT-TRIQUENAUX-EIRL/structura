<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembershipRepository")
 */
class Membership
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MembershipType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="membership", cascade={"persist", "remove"})
     */
    private $payment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\People", mappedBy="memberships", cascade={"persist", "remove"})
     *
     */
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getType(): ?MembershipType
    {
        return $this->type;
    }

    public function setType(?MembershipType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function setMembers($members): self
    {
        foreach($this->members as $oldMember)
        {
            // unset the owning side of the relation
            if ($oldMember->getMemberships()->contains($this))
            {
                $oldMember->removeMembership($this);
            }
        }

        $this->members = new ArrayCollection();

        foreach($members as $newMember)
        {
            $this->addMember($newMember);
        }

        return $this;
    }

    /**
     * Add a person to the membership
     *
     * @param People $people The person to add.
     */
    public function addMember(People $people)
    {
        $this->members[] = $people;

        // set the owning side of the relation if necessary
        if (!$people->getMemberships()->contains($this))
        {
            $people->addMembership($this);
        }
    }

    /**
     * Remove a person from the membership
     *
     * @param People $people The person to remove.
     */
    public function removeMember(People $people)
    {
        $index = $this->members->indexOf($people);

        unset($members[$index]);

        // unset the owning side of the relation if necessary
        if ($people->getMemberships()->contains($this))
        {
            $people->removeMembership($this);
        }
    }
}
