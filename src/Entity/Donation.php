<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DonationRepository")
 */
class Donation
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $donation_date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="donation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\People", inversedBy="donations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $donator;

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
        if ($amount <= 0)
        {
            throw new \InvalidArgumentException('The donation amount should be greater than 0');
        }

        $this->amount = $amount;

        return $this;
    }

    public function getDonationDate(): ?\DateTimeInterface
    {
        return $this->donation_date;
    }

    public function setDonationDate(?\DateTimeInterface $donation_date): self
    {
        $this->donation_date = $donation_date;

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

    public function getDonator(): ?People
    {
        return $this->donator;
    }

    public function setDonator(?People $donator): self
    {
        $this->donator = $donator;

        return $this;
    }
}
