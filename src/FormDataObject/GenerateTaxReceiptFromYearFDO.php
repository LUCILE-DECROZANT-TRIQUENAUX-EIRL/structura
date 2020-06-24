<?php
namespace App\FormDataObject;

class GenerateTaxReceiptFromYearFDO
{
    /**
     * @var int
     */
    private $year;

    /**
     * Get the value of year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @param int $year
     *
     * @return self
     */
    public function setYear(int $year)
    {
        $this->year = $year;

        return $this;
    }
}