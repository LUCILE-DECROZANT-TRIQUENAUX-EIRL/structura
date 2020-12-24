<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="datetime")
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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bank", inversedBy="payments")
     */
    private $bank;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Receipt", mappedBy="payment", cascade={"persist", "remove"})
     */
    private $receipt;

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

    /**
     * Set the payment amount
     *
     * @param float $amount Must be greater than 0
     * @return \self
     * @throws \InvalidArgumentException
     */
    public function setAmount(float $amount): self
    {
        if ($amount <= 0)
        {
            throw new \InvalidArgumentException('The payment amount should be greater than 0');
        }

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
    public function getDonation(): ?Donation
    {
        return $this->donation;
    }

    public function setDonation(Donation $donation): self
    {
        $this->donation = $donation;

        // set (or unset) the owning side of the relation if necessary
        $newPayment = $donation === null ? null : $this;
        if ($newPayment !== $donation->getPayment()) {
            $donation->setPayment($newPayment);
        }

        return $this;
    }

    function getBank()
    {
        return $this->bank;
    }

    /**
     * Set Bank for this payment
     *
     * @param \App\Entity\Bank $bank Bank to set for this payment
     * @return \self
     * @throws \Exception If bank information is not needed in the
     *                    corresponding payment type, throws an exception
     */
    function setBank(?Bank $bank): self
    {
        if (!$this->type->isBankneeded() && !is_null($bank))
        {
            throw new \Exception('Bank information is not needed in this payment.');
        }
        $this->bank = $bank;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(Receipt $receipt): self
    {
        $this->receipt = $receipt;

        // set the owning side of the relation if necessary
        if ($receipt->getPayment() !== $this) {
            $receipt->setPayment($this);
        }

        return $this;
    }
}
