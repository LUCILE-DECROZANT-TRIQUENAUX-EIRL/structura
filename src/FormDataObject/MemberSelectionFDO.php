<?php
namespace App\FormDataObject;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\People;
use Doctrine\Common\Collections\ArrayCollection;

class MemberSelectionFDO
{

    /**
     * @var People[]
     */
    private $newMembers;

    public function __construct()
    {
        $this->newMembers = new ArrayCollection();
    }


    /**
     * Get the value of newMembers
     *
     * @return People[]
     */
    public function getNewMembers()
    {
        return $this->newMembers;
    }

    /**
     * Set the value of newMembers
     *
     * @param People[] $newMembers
     *
     * @return self
     */
    public function setNewMembers(ArrayCollection $newMembers)
    {
        $this->newMembers = $newMembers;

        return $this;
    }

    /**
     * Add a newMember to the MemberSelectionFDO
     *
     * @param People $newMember The newMember to add.
     */
    public function addNewMember(People $newMember)
    {
        if (!$this->hasMember($newMember))
        {
            $this->newMembers[] = $newMember;
        }
    }

    /**
     * Remove a newMember from the MemberSelectionFDO
     *
     * @param People $newMemberToRemove The newMember to remove.
     */
    function removeNewMember(People $newMemberToRemove)
    {
        $ownedNewMembers = $this->getNewMembers();
        foreach ($ownedNewMembers as $index => $ownedNewMember)
        {
            if ($ownedNewMember->getId() === $newMemberToRemove->getId())
            {
                unset($ownedNewMembers[$index]);
            }
        }
    }

    /**
     * Check if this member is already selected
     *
     * @param People $newMember The newMember to add.
     *
     * @return boolean
     */
    function hasMember(People $newMember)
    {
        if (count($this->newMembers) > 0)
        {
            foreach ($this->newMembers as $ownedNewMember)
            {
                if ($ownedNewMember->getId() === $newMember->getId())
                {
                    return true;
                }
            }
        }

        return false;
    }
}