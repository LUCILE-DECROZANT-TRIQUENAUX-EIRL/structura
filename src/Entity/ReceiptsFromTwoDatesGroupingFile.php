<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptsGenerationFromTwoDatesRepository")
 */
class ReceiptsFromTwoDatesGroupingFile
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
    private $dateFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ReceiptsGroupingFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiptsGenerationBase;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getReceiptsGenerationBase(): ?ReceiptsGroupingFile
    {
        return $this->receiptsGenerationBase;
    }

    public function setReceiptsGenerationBase(ReceiptsGroupingFile $receiptsGenerationBase): self
    {
        $this->receiptsGenerationBase = $receiptsGenerationBase;

        return $this;
    }
}
