<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MembershipType", inversedBy="memberships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="membership", cascade={"persist", "remove"})
     */
    private $payment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\People", mappedBy="membership", cascade={"persist", "remove"})
     */
    private $member;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getMember(): ?People
    {
        return $this->member;
    }

    public function setMember(?People $member): self
    {
        $this->member = $member;

        // set (or unset) the owning side of the relation if necessary
        $newMembership = $member === null ? null : $this;
        if ($newMembership !== $member->getMembership()) {
            $member->setMembership($newMembership);
        }

        return $this;
    }
}
