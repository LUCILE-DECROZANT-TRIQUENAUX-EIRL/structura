<?php
namespace App\FormDataObject;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class UpdateUserGeneralDataFDO
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var Responsibility[]
     */
    private $responsibilities;


    /**
     *
     */
    public static function fromUser(User $user): self
    {
        $updateUserGeneralDataFDO = new self();
        $updateUserGeneralDataFDO->username = $user->getUsername();
        $updateUserGeneralDataFDO->responsibilities = $user->getResponsibilities();

        return $updateUserGeneralDataFDO;
    }

    /**
     * Set username
     *
     * @param string $username The username of the UpdateUserGeneralDataFDO.
     *
     * @return UpdateUserGeneralDataFDO
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get UpdateUserGeneralDataFDO's responsibilities
     *
     * @return Responsibility[]
     */
    function getResponsibilities()
    {
        return $this->responsibilities;
    }

    /**
     * Set UpdateUserGeneralDataFDO's responsibilities
     *
     * @param Responsibility[] $responsibilities The responsibilities to set.
     *
     * @return UpdateUserGeneralDataFDO
     */
    function setResponsibilities(array $responsibilities)
    {
        $this->responsibilities = $responsibilities;
        return $this;
    }

    /**
     * Add a responsibility to the updateUserGeneralDataFDO
     *
     * @param Responsibility $responsibility The responsibility to add.
     */
    public function addResponsibility(Responsibility $responsibility)
    {
        if (!$this->hasResponsibility($responsibility))
        {
            $this->responsibilities[] = $responsibility;
        }
    }

    /**
     * Remove a responsibility from the updateUserGeneralDataFDO
     *
     * @param Responsibility $responsibilityToRemove The responsibility to remove.
     */
    function removeResponsibility(Responsibility $responsibilityToRemove)
    {
        $ownedResponsibilities = $this->getResponsibilities();
        foreach ($ownedResponsibilities as $index => $ownedResponsibility)
        {
            if ($ownedResponsibility->getId() === $responsibilityToRemove->getId())
            {
                unset($ownedResponsibilities[$index]);
            }
        }
    }

    /**
     * Check if user has this responsibility
     *
     * @param Responsibility $responsibility The responsibility to add.
     *
     * @return boolean
     */
    function hasResponsibility(Responsibility $responsibility)
    {
        foreach ($this->getResponsibilities() as $ownedResponsibility)
        {
            if ($ownedResponsibility->getId() === $responsibility->getId())
            {
                return true;
            }
        }
        return false;
    }
}