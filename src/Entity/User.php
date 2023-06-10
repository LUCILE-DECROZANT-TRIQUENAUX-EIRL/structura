<?php
/**
 * @package App\Entity
 * @version 1.0
 * @author Lucile-DT
 * @license GPU v3.0
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * User is a class used to connect to the application. It contains the basic
 * information needed for this.
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(
 *      fields={"username"},
 *      message="Ce nom d'utilisateurice n'est pas disponible."
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string Field used to store the plain password in order
     * to populate the $password var with en encrypted value.
     */
    private $plainPassword;

    /**
     * @var Responsibility[]
     *
     * @ORM\ManyToMany(targetEntity="Responsibility")
     * @ORM\JoinTable(
     *      name="users_responsibilities",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="responsibility_id", referencedColumnName="id")}
     * )
     */
    private $responsibilities;

    /**
     * More generic data stored about the user.
     *
     * @var type \App\Entity\People
     *
     * @OneToOne(targetEntity="People", mappedBy="user")
     */
    private $people;

    /**
     * The list of all the update of the user.
     *
     * @var UserUpdater[]
     *
     * @ORM\OneToMany(targetEntity="UserUpdater", mappedBy="updater")
     */
    private $updaters;

    /**
     *
     */
    function __construct($id = -1, $username = NULL, $plainPassword = NULL, $responsibilities = [])
    {
        $this->id = $id;
        $this->username = $username;
        $this->plainPassword = $plainPassword;
        $this->responsibilities = $responsibilities;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username The username of the user.
     *
     * @return User
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
     * Get the property used to identify an user.
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * Get plainPassword
     *
     * @return string
     */
    function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set plainPassword
     *
     * @param string $plainPassword The password in plain text.
     *
     * @return User
     */
    function setPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password The encoded password.
     *
     * @return User
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Get user's responsibilities
     *
     * @return Responsibility[]
     */
    function getResponsibilities()
    {
        return $this->responsibilities;
    }

    /**
     * Set user's responsibilities
     *
     * @param Responsibility[] $responsibilities The responsibilities to set.
     *
     * @return User
     */
    function setResponsibilities(array $responsibilities)
    {
        $this->responsibilities = $responsibilities;
        return $this;
    }

    /**
     * Add a responsibility to the user
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
     * Remove a responsibility from the user
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

    /**
     * Get the value of updaters
     *
     * @return UserUpdater[]
     */
    public function getUpdaters()
    {
        return $this->updaters;
    }

    /**
     * Set the value of updaters
     *
     * @param UserUpdater[] $updaters The updater to set.
     *
     * @return User
     */
    public function setUpdaters(array $updaters)
    {
        $this->updaters = $updaters;

        return $this;
    }

    /**
     * Add an updater to the user
     *
     * @param UserUpdater $updater The updater to add.
     */
    public function addUpdater(UserUpdater $updater)
    {
        $this->updaters[] = $updater;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * Return an array of strings containing the role list
     * (each string being a responsibility label used by Symfony to define a user's role)
     *
     * @return string[]
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->responsibilities as $responsibility)
        {
            $roles[] = $responsibility->getCode();
        }
        return $roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Return the People corresponding to this user, if there is one.
     *
     * @return \App\Entity\People
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * Set the People corresponding to this user.
     *
     * @param \App\Entity\People $people
     */
    public function setPeople(People $people = null)
    {
        $this->people = $people;
        return $this;
    }
}
