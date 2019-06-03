<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 * @UniqueEntity(
 *      fields={"username"},
 *      message="Ce nom d'utilisateurice n'est pas disponible."
 * )
 */
class User implements UserInterface
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
     * @ORM\ManyToMany(targetEntity="Responsability")
     * @ORM\JoinTable(
     *      name="users_responsabilities",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="responsability_id", referencedColumnName="id")}
     * )
     */
    private $responsabilities;

    /**
     *
     */
    function __construct($id = -1, $username = NULL, $plainPassword = NULL, $responsabilities = [])
    {
        $this->id = $id;
        $this->username = $username;
        $this->plainPassword = $plainPassword;
        $this->responsabilities = $responsabilities;

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
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
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
     * @param string $plainPassword
     *
     * @return User
     */
    function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get user's responsabilities
     *
     * @return Responsability[]
     */
    function getResponsabilities()
    {
        return $this->responsabilities;
    }

    /**
     * Set user's responsabilities
     *
     * @param Responsability[] $responsabilities
     *
     * @return User
     */
    function setResponsabilities($responsabilities)
    {
        $this->responsabilities = $responsabilities;
        return $this;
    }

    /**
     * Add a responsability to the user
     *
     * @param String $responsabilityName Name of the wanted responsability
     */
    public function addResponsability($responsabilityName)
    {
        $responsabilityNameUpperCase = strtoupper($responsabilityName);
        if (substr($responsabilityNameUpperCase, 0, 5) !== "ROLE_")
        {
            $responsabilityNameUpperCase = sprintf('ROLE_%s', $responsabilityNameUpperCase);
        }
        $responsability = new Responsability();
        $responsability->setLabel($responsabilityNameUpperCase);
        $this->responsabilities[] = $responsability;
    }

    public function eraseCredentials()
    {

    }

    /**
     * Return an array of strings containing the role list
     * (each string being a responsability label used by Symfony to define a user's role)
     *
     * @return string[]
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->responsabilities as $responsability)
        {
            $roles[] = $responsability->getCode();
        }
        return $roles;
    }

    public function getSalt()
    {

    }

}
