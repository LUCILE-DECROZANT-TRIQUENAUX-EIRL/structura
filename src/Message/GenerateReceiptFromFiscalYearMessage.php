<?php

namespace App\Message;

class GenerateReceiptFromFiscalYearMessage
{
    private $fiscalYear;

    public function __construct(string $fiscalYear)
    {
        $this->fiscalYear = $fiscalYear;
    }

    public function getFiscalYear(): string
    {
        return $this->fiscalYear;
    }
}