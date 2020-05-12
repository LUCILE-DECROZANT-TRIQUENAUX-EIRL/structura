<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptsFromFiscalYearGroupingFileRepository")
 */
class ReceiptsFromFiscalYearGroupingFile
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
    private $fiscalYear;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ReceiptsGroupingFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiptsGenerationBase;

    public function getId(): ?int
    {
        return $this->id;
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
