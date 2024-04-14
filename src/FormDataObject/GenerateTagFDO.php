<?php

namespace App\FormDataObject;

class GenerateTagFDO
{
    const DEPARTMENT_AIN = '01';
    const DEPARTMENT_ISERE = '38';
    const DEPARTMENT_LOIRE = '42';
    const DEPARTMENT_RHONE = '69';
    const DEPARTMENT_OTHER = 'Autres';

    private ?int $year;

    private array $departments;

    function __construct() {
        $this->year = null;
        $this->departments = [
            self::DEPARTMENT_AIN,
            self::DEPARTMENT_ISERE,
            self::DEPARTMENT_LOIRE,
            self::DEPARTMENT_RHONE,
            self::DEPARTMENT_OTHER,
        ];
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of departments
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set the value of departments
     *
     * @return  self
     */
    public function setDepartments($departments)
    {
        $this->departments = $departments;

        return $this;
    }

    /**
     * Add a department
     *
     * @param string $department
     * @return  self
     */
    public function addDepartment($department)
    {
        $this->departments[] = $department;

        return $this;
    }

    /**
     * Remove a department from the list
     *
     * @param string $department The department to remove.
     */
    public function removeDepartment($department)
    {
        $index = array_search($department, $this->departments);

        unset($this->departments[$index]);
    }
}
