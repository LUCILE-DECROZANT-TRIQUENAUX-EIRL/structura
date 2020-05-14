<?php
namespace App\FormDataObject;

class GenerateTaxReceiptFromTwoDatesFDO
{
    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    /**
     * Get the value of from
     *
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the value of from
     *
     * @param \DateTime $from
     *
     * @return self
     */
    public function setFrom(\DateTime $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get the value of to
     *
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the value of to
     *
     * @param \DateTime $to
     *
     * @return self
     */
    public function setTo(\DateTime $to)
    {
        $this->to = $to;

        return $this;
    }
}