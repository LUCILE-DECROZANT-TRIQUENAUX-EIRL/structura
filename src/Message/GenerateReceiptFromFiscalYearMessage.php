<?php

namespace App\Message;


class GenerateReceiptFromFiscalYearMessage
{
    private $fiscalYear;
    private $userId;

    public function __construct(string $fiscalYear, int $userId)
    {
        $this->fiscalYear = $fiscalYear;
        $this->userId = $userId;
    }

    public function getFiscalYear(): string
    {
        return $this->fiscalYear;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}