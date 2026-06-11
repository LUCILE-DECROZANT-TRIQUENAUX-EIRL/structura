<?php

namespace App\FormDataObject;

use App\Entity\DonationOrigin;

class GenerateTagFDO
{
    const DEPARTMENT_AIN = '01';
    const DEPARTMENT_ISERE = '38';
    const DEPARTMENT_LOIRE = '42';
    const DEPARTMENT_RHONE = '69';
    const DEPARTMENT_OTHER = 'Autres';

    private array $membershipYears;

    private array $donationYears;

    private array $departments;

    private array $donationOrigins;

    private bool $physicalMailOnly;

    function __construct() {
        $this->membershipYears = [];
        $this->donationYears = [];
        $this->departments = [
            self::DEPARTMENT_AIN,
            self::DEPARTMENT_ISERE,
            self::DEPARTMENT_LOIRE,
            self::DEPARTMENT_RHONE,
            self::DEPARTMENT_OTHER,
        ];
        $this->donationOrigins = [];
        $this->physicalMailOnly = false;
    }

    /**
     * Get the value of membershipYears
     */
    public function getMembershipYears(): array
    {
        return $this->membershipYears;
    }

    /**
     * Set the value of membershipYear
     *
     * @return  self
     */
    public function setMembershipYears(array $membershipYears): self
    {
        $this->membershipYears = $membershipYears;

        return $this;
    }

    /**
     * Add a membershipYear
     *
     * @param int $membershipYear
     * @return  self
     */
    public function addMembershipYear(int $membershipYear): self
    {
        $this->membershipYears[] = $membershipYear;

        return $this;
    }

    /**
     * Remove a membershipYear from the list
     *
     * @param int $membershipYear The membershipYear to remove.
     */
    public function removeMembershipYear(int $membershipYear): void
    {
        $index = array_search($membershipYear, $this->membershipYears);

        unset($this->membershipYears[$index]);
    }

    /**
     * Get the value of departments
     */
    public function getDepartments(): array
    {
        return $this->departments;
    }

    /**
     * Set the value of departments
     *
     * @return  self
     */
    public function setDepartments(array $departments): self
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
    public function addDepartment(string $department): self
    {
        $this->departments[] = $department;

        return $this;
    }

    /**
     * Remove a department from the list
     *
     * @param string $department The department to remove.
     */
    public function removeDepartment(string $department): void
    {
        $index = array_search($department, $this->departments);

        unset($this->departments[$index]);
    }

    /**
     * Get the value of donationOrigins
     */    public function getDonationOrigins(): array
    {
        return $this->donationOrigins;
    }

    /**
     * Set the value of donationOrigins
     *
     * @return  self
     */    public function setDonationOrigins(array $donationOrigins): self
    {
        $this->donationOrigins = $donationOrigins;

        return $this;
    }

    /**
     * Add a donation origin
     *
     * @param DonationOrigin $donationOrigin
     * @return  self
     */
    public function addDonationOrigin(DonationOrigin $donationOrigin): self
    {
        $this->donationOrigins[] = $donationOrigin;

        return $this;
    }

    /*
     * Remove a donation origin from the list
     *
     * @param DonationOrigin $donationOrigin The donation origin to remove.
     */
    public function removeDonationOrigin(DonationOrigin $donationOrigin): void
    {
        $index = array_search($donationOrigin, $this->donationOrigins);

        unset($this->donationOrigins[$index]);
    }

    /**
     * Get the value of donationYears
     */
    public function getDonationYears(): array
    {
        return $this->donationYears;
    }

    /**
     * Set the value of donationYear
     *
     * @return  self
     */
    public function setDonationYears(array $donationYears): self
    {
        $this->donationYears = $donationYears;

        return $this;
    }

    /**
     * Add a donationYear
     *
     * @param int $donationYear
     * @return  self
     */
    public function addDonationYear(int $donationYear): self
    {
        $this->donationYears[] = $donationYear;

        return $this;
    }

    /**
     * Remove a donationYear from the list
     *
     * @param int $donationYear The donationYear to remove.
     */
    public function removeDonationYear(int $donationYear): void
    {
        $index = array_search($donationYear, $this->donationYears);

        unset($this->donationYears[$index]);
    }

    /**
     * Get the value of physicalMailOnly
     */    public function isPhysicalMailOnly(): bool
    {
        return $this->physicalMailOnly;
    }

    /**
     * Set the value of physicalMailOnly
     *
     * @return  self
     */    public function setPhysicalMailOnly(bool $physicalMailOnly): self
    {
        $this->physicalMailOnly = $physicalMailOnly;

        return $this;
    }
}
