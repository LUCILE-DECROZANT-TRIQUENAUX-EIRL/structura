<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserUpdater
 *
 * @ORM\Table(name="user_updater")
 * @ORM\Entity(repositoryClass="App\Repository\UserUpdaterRepository")
 */
class UserUpdater
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="updaters")
     * @ORM\JoinColumn(name="updater_id", referencedColumnName="id", nullable=false)
     */
    private $updater;

    /**
     * @var \DateTime
     *
     * @ORM\Id
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     *
     */
    function __construct($user = NULL, $updater = NULL ,$date = NULL)
    {
        $this->user = $user;
        $this->updater = $updater;
        $this->date = $date;
    }


    /**
     * Set user
     *
     * @param User $user The use to update.
     *
     * @return UserUpdater
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set updater
     *
     * @param User $updater The User to update.
     *
     * @return UserUpdater
     */
    public function setUpdater(User $updater)
    {
        $this->updater = $updater;

        return $this;
    }

    /**
     * Get updater
     *
     * @return User
     */
    public function getUpdater()
    {
        return $this->updater;
    }

    /**
     * Set date
     *
     * @param \DateTime $date The date.
     *
     * @return UserUpdater
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
