<?php
namespace App\FormDataObject;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\People;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\Membership;
use App\Entity\MembershipType;
use Doctrine\Common\Collections\ArrayCollection;

class CreateMembershipFDO
{
    private $amount;

    private $date_start;

    private $date_end;

    private $comment;

    private $membershipType;

    private $payment;

    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
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

    public function setMembers(?People $members): self
    {
        $this->members = $members;

        return $this;
    }

    /**
     * Add a person to the membership
     *
     * @param People $people The person to add.
     */
    public function addMember($people)
    {
        $this->members[] = $people;

        // set the owning side of the relation if necessary
        if (!in_array($this, $people->getMemberships())) {
            $people->addMembership($this);
        }
    }

    /**
     * Remove a person from the membership
     *
     * @param People $people The person to remove.
     */
    public function removeMember($people)
    {
        $index = array_search($people, $this->members);

        unset($members[$index]);

        // unset the owning side of the relation if necessary
        if (in_array($this, $people->getMemberships())) {
            $people->removeMembership($this);
        }
    }
}