<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptsGenerationRepository")
 */
class ReceiptsGeneration
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
    private $generationDateStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $generationDateEnd;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $filename;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Receipt", inversedBy="receiptsGenerations")
     */
    private $receipts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $generator;

    public function __construct()
    {
        $this->receipts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenerationDateStart(): ?\DateTimeInterface
    {
        return $this->generationDateStart;
    }

    public function setGenerationDateStart(\DateTimeInterface $generationDateStart): self
    {
        $this->generationDateStart = $generationDateStart;

        return $this;
    }

    public function getGenerationDateEnd(): ?\DateTimeInterface
    {
        return $this->generationDateEnd;
    }

    public function setGenerationDateEnd(?\DateTimeInterface $generationDateEnd): self
    {
        $this->generationDateEnd = $generationDateEnd;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return Collection|Receipt[]
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
    {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
        }

        return $this;
    }

    public function getGenerator(): ?User
    {
        return $this->generator;
    }

    public function setGenerator(?User $generator): self
    {
        $this->generator = $generator;

        return $this;
    }
}
