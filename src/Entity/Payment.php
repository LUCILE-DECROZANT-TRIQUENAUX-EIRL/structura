<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_received;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_cashed;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=3000, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentType", inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Membership", mappedBy="payment", cascade={"persist", "remove"})
     */
    private $membership;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Donation", mappedBy="payment")
     */
    private $donation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\People", inversedBy="payments")
     */
    private $payer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReceived(): ?\DateTimeInterface
    {
        return $this->date_received;
    }

    public function setDateReceived(?\DateTimeInterface $date_received): self
    {
        $this->date_received = $date_received;

        return $this;
    }

    public function getDateCashed(): ?\DateTimeInterface
    {
        return $this->date_cashed;
    }

    public function setDateCashed(?\DateTimeInterface $date_cashed): self
    {
        $this->date_cashed = $date_cashed;

        return $this;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getType(): ?PaymentType
    {
        return $this->type;
    }

    public function setType(?PaymentType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(?Membership $membership): self
    {
        $this->membership = $membership;

        // set (or unset) the owning side of the relation if necessary
        $newPayment = $membership === null ? null : $this;
        if ($newPayment !== $membership->getPayment()) {
            $membership->setPayment($newPayment);
        }

        return $this;
    }

    function getPayer(): People
    {
        return $this->payer;
    }

    function setPayer($payer): self
    {
        $this->payer = $payer;
        return $this;
    }


    /**
     * @return Donation
     */
    public function getDonation(): Donation
    {
        return $this->donations;
    }

    public function setDonation(Donation $donation): self
    {
        $this->donation = $donation;

        return $this;
    }
}
