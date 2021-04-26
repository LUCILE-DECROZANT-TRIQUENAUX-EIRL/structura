<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentTypeRepository")
 */
class PaymentType
{
    const HELLO_ASSO_LABEL = 'HelloAsso';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="type")
     */
    private $payments;

    /**
     * Boolean to check in the Payment of this type if Bank information
     * is needed.
     *
     * @var boolean True is Bank is needed in payment, false otherwise
     *
     * @ORM\Column(type="boolean", length=255)
     */
    private $isBankneeded;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): ArrayCollection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setType($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getType() === $this) {
                $payment->setType(null);
            }
        }

        return $this;
    }

    function isBankneeded(): bool
    {
        return $this->isBankneeded;
    }

    function setIsBankneeded(bool $isBankneeded): self
    {
        $this->isBankneeded = $isBankneeded;

        return $this;
    }
}
