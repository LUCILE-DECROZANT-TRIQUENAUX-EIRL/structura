<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptsFromYearGroupingFileRepository")
 */
class ReceiptsFromYearGroupingFile
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
    private $year;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ReceiptsGroupingFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiptsGenerationBase;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

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
