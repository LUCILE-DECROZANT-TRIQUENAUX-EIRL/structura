<?php

namespace App\FormDataObject;

use Symfony\Component\Validator\Constraints as Assert;
use \App\Entity\Donation;
use \App\Entity\Payment;

class UpdateDonationFDO
{

    private $donator;

    private $amount;

    private $donationDate;

    private $paymentType;

    private $cashedDate;

    public function __construct(Donation $donation = null)
    {
        if ($donation !== null)
        {
            $payment = $donation->getPayment();

            $this->donator = $donation->getDonator();
            $this->amount = $donation->getAmount();
            $this->donationDate = $donation->getDonationDate();
            $this->paymentType = $payment->getType();
            $this->cashedDate = $payment->getDateCashed();
        }
    }

    function getDonator()
    {
        return $this->donator;
    }

    function getAmount()
    {
        return $this->amount;
    }

    function getDonationDate()
    {
        return $this->donationDate;
    }

    function getPaymentType()
    {
        return $this->paymentType;
    }

    function getCashedDate()
    {
        return $this->cashedDate;
    }

    function setDonator($donator): self
    {
        $this->donator = $donator;
        return $this;
    }

    function setAmount($amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    function setDonationDate($donationDate): self
    {
        $this->donationDate = $donationDate;
        return $this;
    }

    function setPaymentType($paymentType): self
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    function setCashedDate($cashedDate): self
    {
        $this->cashedDate = $cashedDate;
        return $this;
    }

}
