<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptRepository")
 */
class Receipt
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The receipt's order number.
     * This value is incremented at each receipt creation for a same fiscal year.
     *
     * Once set, this value can't be edited.
     *
     * @ORM\Column(type="integer")
     */
    private $orderNumber;

    /**
     * Code provided as an identifier for the receipt,
     * used by French administration
     *
     * Currently has the form 'YYYY-NNNN' where YYYY is the fiscal year of
     * the receipt and NNNN the order number of the receipt
     *
     * Once set, this value can't be edited.
     *
     * @ORM\Column(type="string", length=9)
     */
    private $orderCode;

    /**
     * The receipt's fiscal year.
     *
     * @ORM\Column(type="integer")
     */
    private $fiscalYear;

    /**
     * The payment corresponding to this receipt.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="receipt", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * A list of all receipts generations where this receipt has been used.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ReceiptsGroupingFile", mappedBy="receipts")
     */
    private $receiptsGenerations;

    public function __construct()
    {
        $this->receiptsGenerations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     *
     * @return \self
     * @throws \Exception
     */
    public function generateOrderCode(): self
    {
        if (empty($this->fiscalYear))
        {
            $message = 'The fiscal year of this receipt is missing';
            throw new \Exception($message);
        }

        if (empty($this->orderNumber))
        {
            $message = 'The order number of this receipt is missing';
            throw new \Exception($message);
        }

        $numberOfDigits = floor(log10($this->orderNumber) + 1);
        $numberOf0ToAdd = 4 - $numberOfDigits;
        $orderNumberPart = str_repeat('0', $numberOf0ToAdd) . $this->orderNumber;

        // Format : YYYY-000N
        $this->orderCode = $this->fiscalYear . '-' . $orderNumberPart;

        return $this;
    }

    public function getOrderCode(): string
    {
        return $this->orderCode;
    }

    /**
     * Set the order code using fiscal year and order number of
     * the receipt
     *
     * @return \self
     * @throws \Exception
     */
    public function setOrderCode(): self
    {
        if (empty($this->fiscalYear))
        {
            $message = 'The fiscal year of this receipt is missing';
            throw new \Exception($message);
        }

        if (empty($this->orderNumber))
        {
            $message = 'The order number of this receipt is missing';
            throw new \Exception($message);
        }

        if (!empty($this->orderCode))
        {
            $message = 'The order code of this receipt is already set';
            throw new \Exception($message);
        }

        $this->generateOrderCode();

        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(int $orderNumber): self
    {
        if (empty($this->orderNumber))
        {
            $message = 'The order code of this receipt is already set';
            throw new \Exception($message);
        }

        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getFiscalYear(): ?int
    {
        return $this->fiscalYear;
    }

    public function setFiscalYear(int $fiscalYear): self
    {
        $this->fiscalYear = $fiscalYear;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return Collection|ReceiptsGroupingFile[]
     */
    public function getReceiptsGenerations(): Collection
    {
        return $this->receiptsGenerations;
    }

    public function addReceiptsGeneration(ReceiptsGroupingFile $receiptsGeneration): self
    {
        if (!$this->receiptsGenerations->contains($receiptsGeneration)) {
            $this->receiptsGenerations[] = $receiptsGeneration;
            $receiptsGeneration->addReceipt($this);
        }

        return $this;
    }

    public function removeReceiptsGeneration(ReceiptsGroupingFile $receiptsGeneration): self
    {
        if ($this->receiptsGenerations->contains($receiptsGeneration)) {
            $this->receiptsGenerations->removeElement($receiptsGeneration);
            $receiptsGeneration->removeReceipt($this);
        }

        return $this;
    }
}
