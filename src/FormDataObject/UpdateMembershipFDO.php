<?php
namespace App\FormDataObject;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\People;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\Membership;
use App\Entity\MembershipType;
use Doctrine\Common\Collections\ArrayCollection;

class UpdateMembershipFDO
{
    private $isMembershipAndDonation;

    private $membershipType;

    private $membershipAmount;

    private $membershipDate_start;

    private $membershipDate_end;

    private $membershipFiscal_year;

    private $membershipComment;

    private $donationAmount;

    private $paymentType;

    private $paymentAmount;

    private $paymentDate_received;

    private $paymentDate_cashed;

    private $payer;

    private $members;

    private $newMember;

    public function __construct(Membership $membership = null, Donation $donation = null)
    {
        $this->isMembershipAndDonation = false;

        if ($membership !== null)
        {
            $this->members = $membership->getMembers();

            $this->membershipType = $membership->getType();
            $this->membershipAmount = $membership->getAmount();
            $this->membershipDate_start = $membership->getDateStart();
            $this->membershipDate_end = $membership->getDateEnd();
            $this->membershipFiscal_year = $membership->getFiscalYear();
            $this->membershipComment = $membership->getComment();

            $payment = $membership->getPayment();

            $this->paymentType = $payment->getType();
            $this->paymentAmount = $payment->getAmount();
            $this->paymentDate_received = $payment->getDateReceived();
            $this->paymentDate_cashed = $payment->getDateCashed();

            if ($donation !== null)
            {
                $this->donationAmount = $donation->getAmount();
                $this->isMembershipAndDonation = true;
            }
        }
        else
        {
            $this->members = new ArrayCollection();
        }
    }

    public function getMembershipAmount(): ?float
    {
        return $this->membershipAmount;
    }

    public function setMembershipAmount(float $membershipAmount): self
    {
        $this->membershipAmount = $membershipAmount;

        return $this;
    }

    public function getMembershipDateStart(): ?\DateTimeInterface
    {
        return $this->membershipDate_start;
    }

    public function setMembershipDateStart(\DateTimeInterface $membershipDate_start): self
    {
        $this->membershipDate_start = $membershipDate_start;

        return $this;
    }

    public function getMembershipDateEnd(): ?\DateTimeInterface
    {
        return $this->membershipDate_end;
    }

    public function setMembershipDateEnd(\DateTimeInterface $membershipDate_end): self
    {
        $this->membershipDate_end = $membershipDate_end;

        return $this;
    }

    /**
     * Get the value of membershipFiscal_year
     */
    public function getMembershipFiscalYear()
    {
        return $this->membershipFiscal_year;
    }

    /**
     * Set the value of membershipFiscal_year
     *
     * @return  self
     */
    public function setMembershipFiscalYear($membershipFiscal_year)
    {
        $this->membershipFiscal_year = $membershipFiscal_year;

        return $this;
    }

    public function getMembershipComment(): ?string
    {
        return $this->membershipComment;
    }

    public function setMembershipComment(?string $membershipComment): self
    {
        $this->membershipComment = $membershipComment;

        return $this;
    }

    public function getMembershipType(): ?MembershipType
    {
        return $this->membershipType;
    }

    public function setMembershipType(?MembershipType $membershipType): self
    {
        $this->membershipType = $membershipType;

        return $this;
    }

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

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
    public function addMembers($people)
    {
        $this->members[] = $people;
    }

    /**
     * Remove a person from the membership
     *
     * @param People $people The person to remove.
     */
    public function removeMembers($people)
    {
        $index = array_search($people, $this->members);

        unset($members[$index]);
    }

    /**
     * Get the value of donationAmount
     */
    public function getDonationAmount()
    {
        return $this->donationAmount;
    }

    /**
     * Set the value of donationAmount
     *
     * @return  self
     */
    public function setDonationAmount($donationAmount)
    {
        $this->donationAmount = $donationAmount;

        return $this;
    }

    /**
     * Get the value of paymentDate_received
     */
    public function getPaymentDateReceived()
    {
        return $this->paymentDate_received;
    }

    /**
     * Set the value of paymentDate_received
     *
     * @return  self
     */
    public function setPaymentDateReceived($paymentDate_received)
    {
        $this->paymentDate_received = $paymentDate_received;

        return $this;
    }

    /**
     * Get the value of paymentDate_cashed
     */
    public function getPaymentDateCashed()
    {
        return $this->paymentDate_cashed;
    }

    /**
     * Set the value of paymentDate_cashed
     *
     * @return  self
     */
    public function setPaymentDateCashed($paymentDate_cashed)
    {
        $this->paymentDate_cashed = $paymentDate_cashed;

        return $this;
    }

    /**
     * Get the value of paymentAmount
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set the value of paymentAmount
     *
     * @return  self
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get the value of isMembershipAndDonation
     */
    public function getIsMembershipAndDonation()
    {
        return $this->isMembershipAndDonation;
    }

    /**
     * Set the value of isMembershipAndDonation
     *
     * @return  self
     */
    public function setIsMembershipAndDonation($isMembershipAndDonation)
    {
        $this->isMembershipAndDonation = $isMembershipAndDonation;

        return $this;
    }

    /**
     * Get the value of newMember
     */
    public function getNewMember()
    {
        return $this->newMember;
    }

    /**
     * Set the value of newMember
     *
     * @return  self
     */
    public function setNewMember($newMember)
    {
        $this->newMember = $newMember;

        return $this;
    }

    /**
     * Get the value of payer
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set the value of payer
     *
     * @return  self
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }
}