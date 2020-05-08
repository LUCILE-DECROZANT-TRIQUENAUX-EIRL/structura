<?php
namespace App\FormDataObject;

class GenerateTaxReceiptFromFiscalYearFDO
{
    /**
     * @var int
     */
    private $fiscalYear;

    /**
     * Get the value of fiscalYear
     *
     * @return int
     */
    public function getFiscalYear()
    {
        return $this->fiscalYear;
    }

    /**
     * Set the value of fiscalYear
     *
     * @param int $fiscalYear
     *
     * @return self
     */
    public function setFiscalYear(int $fiscalYear)
    {
        $this->fiscalYear = $fiscalYear;

        return $this;
    }
}