<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptsGenerationFromTwoDatesRepository")
 */
class ReceiptsGenerationFromTwoDates
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
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ReceiptsGeneration", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiptsGenerationBase;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getReceiptsGenerationBase(): ?ReceiptsGeneration
    {
        return $this->receiptsGenerationBase;
    }

    public function setReceiptsGenerationBase(ReceiptsGeneration $receiptsGenerationBase): self
    {
        $this->receiptsGenerationBase = $receiptsGenerationBase;

        return $this;
    }
}
