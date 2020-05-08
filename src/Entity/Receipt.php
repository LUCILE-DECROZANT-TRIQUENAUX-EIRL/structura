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
     * @ORM\Column(type="integer")
     */
    private $orderNum;

    /**
     * @ORM\Column(type="integer")
     */
    private $fiscalYear;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="receipt", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ReceiptsGeneration", mappedBy="receipts")
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

    public function getOrderCode(): string
    {
        return $this->fiscalYear . '-' . $this->orderNum();
    }

    public function getOrderNum(): ?int
    {
        return $this->orderNum;
    }

    public function setOrderNum(int $orderNum): self
    {
        $this->orderNum = $orderNum;

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
     * @return Collection|ReceiptsGeneration[]
     */
    public function getReceiptsGenerations(): Collection
    {
        return $this->receiptsGenerations;
    }

    public function addReceiptsGeneration(ReceiptsGeneration $receiptsGeneration): self
    {
        if (!$this->receiptsGenerations->contains($receiptsGeneration)) {
            $this->receiptsGenerations[] = $receiptsGeneration;
            $receiptsGeneration->addReceipt($this);
        }

        return $this;
    }

    public function removeReceiptsGeneration(ReceiptsGeneration $receiptsGeneration): self
    {
        if ($this->receiptsGenerations->contains($receiptsGeneration)) {
            $this->receiptsGenerations->removeElement($receiptsGeneration);
            $receiptsGeneration->removeReceipt($this);
        }

        return $this;
    }
}
