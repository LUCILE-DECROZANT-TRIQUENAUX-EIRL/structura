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
     * @ORM\ManyToOne(targetEntity="Denomination")
     * @ORM\JoinColumn(name="denomination_id", referencedColumnName="id", nullable=true)
     */
    private $denomination;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=255)
     */
    private $emailAddress;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_receiving_newsletter", type="boolean")
     */
    private $isReceivingNewsletter;

    /**
     * @var bool
     *
     * @ORM\Column(name="newsletter_dematerialization", type="boolean")
     */
    private $newsletterDematerialization;

    /**
     * @var string
     *
     * @ORM\Column(name="home_phone_number", type="string", length=10)
     */
    private $homePhoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="cell_phone_number", type="string", length=10)
     */
    private $cellPhoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="work_phone_number", type="string", length=10)
     */
    private $workPhoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="work_fax_number", type="string", length=10)
     */
    private $workFaxNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="observations", type="string", length=1000)
     */
    private $observations;

    /**
     * @var string
     *
     * @ORM\Column(name="medical_details", type="string", length=1000)
     */
    private $medicalDetails;

    /**
     * @var UserUpdater[]
     *
     * @ORM\OneToMany(targetEntity="UserUpdater", mappedBy="updater")
     */
    private $updaters;

    /**
     * @var Address[]
     *
     * @ORM\ManyToMany(targetEntity="Address")
     * @ORM\JoinTable(
     *      name="users_addresses",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="address_id", referencedColumnName="id")}
     * )
     */
    private $addresses;

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
     * @param Responsibility[] $responsibilities
     *
     * @return User
     */
    function setResponsibilities($responsibilities)
    {
        $this->responsibilities = $responsibilities;
        return $this;
    }

    /**
     * Add a responsibility to the user
     *
     * @param String $responsibilityName Name of the wanted responsibility
     */
    public function addResponsibility($responsibilityName)
    {
        $responsibilityNameUpperCase = strtoupper($responsibilityName);
        if (substr($responsibilityNameUpperCase, 0, 5) !== "ROLE_")
        {
            $responsibilityNameUpperCase = sprintf('ROLE_%s', $responsibilityNameUpperCase);
        }
        $responsibility = new Responsibility();
        $responsibility->setLabel($responsibilityNameUpperCase);
        $this->responsibilities[] = $responsibility;
    }

    /**
     * Get the value of denomination
     */
    public function getDenomination()
    {
        return $this->denomination;
    }

    /**
     * Set the value of denomination
     *
     * @return User
     */
    public function setDenomination($denomination)
    {
        $this->denomination = $denomination;

        return $this;
    }

    /**
     * Get the value of firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of emailAddress
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set the value of emailAddress
     *
     * @param string $emailAddress
     *
     * @return User
     */
    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get the value of isReceivingNewsletter
     *
     * @return bool
     */
    public function getIsReceivingNewsletter()
    {
        return $this->isReceivingNewsletter;
    }

    /**
     * Set the value of isReceivingNewsletter
     *
     * @param bool $isReceivingNewsletter
     *
     * @return User
     */
    public function setIsReceivingNewsletter(bool $isReceivingNewsletter)
    {
        $this->isReceivingNewsletter = $isReceivingNewsletter;

        return $this;
    }

    /**
     * Get the value of newsletterDematerialization
     *
     * @return bool
     */
    public function getNewsletterDematerialization()
    {
        return $this->newsletterDematerialization;
    }

    /**
     * Set the value of newsletterDematerialization
     *
     * @param bool $newsletterDematerialization
     *
     * @return User
     */
    public function setNewsletterDematerialization(bool $newsletterDematerialization)
    {
        $this->newsletterDematerialization = $newsletterDematerialization;

        return $this;
    }

    /**
     * Get the value of homePhoneNumber
     *
     * @return string
     */
    public function getHomePhoneNumber()
    {
        return $this->homePhoneNumber;
    }

    /**
     * Set the value of homePhoneNumber
     *
     * @param string $homePhoneNumber
     *
     * @return User
     */
    public function setHomePhoneNumber(string $homePhoneNumber)
    {
        $this->homePhoneNumber = $homePhoneNumber;

        return $this;
    }

    /**
     * Get the value of cellPhoneNumber
     *
     * @return string
     */
    public function getCellPhoneNumber()
    {
        return $this->cellPhoneNumber;
    }

    /**
     * Set the value of cellPhoneNumber
     *
     * @param string $cellPhoneNumber
     *
     * @return User
     */
    public function setCellPhoneNumber(string $cellPhoneNumber)
    {
        $this->cellPhoneNumber = $cellPhoneNumber;

        return $this;
    }

    /**
     * Get the value of workPhoneNumber
     *
     * @return string
     */
    public function getWorkPhoneNumber()
    {
        return $this->workPhoneNumber;
    }

    /**
     * Set the value of workPhoneNumber
     *
     * @param string $workPhoneNumber
     *
     * @return User
     */
    public function setWorkPhoneNumber(string $workPhoneNumber)
    {
        $this->workPhoneNumber = $workPhoneNumber;

        return $this;
    }

    /**
     * Get the value of workFaxNumber
     *
     * @return string
     */
    public function getWorkFaxNumber()
    {
        return $this->workFaxNumber;
    }

    /**
     * Set the value of workFaxNumber
     *
     * @param string $workFaxNumber
     *
     * @return User
     */
    public function setWorkFaxNumber(string $workFaxNumber)
    {
        $this->workFaxNumber = $workFaxNumber;

        return $this;
    }

    /**
     * Get the value of observations
     *
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Set the value of observations
     *
     * @param string $observations
     *
     * @return User
     */
    public function setObservations(string $observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * Get the value of medicalDetails
     *
     * @return string
     */
    public function getMedicalDetails()
    {
        return $this->medicalDetails;
    }

    /**
     * Set the value of medicalDetails
     *
     * @param string $medicalDetails
     *
     * @return User
     */
    public function setMedicalDetails(string $medicalDetails)
    {
        $this->medicalDetails = $medicalDetails;

        return $this;
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
     * @param UserUpdater[] $updaters
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
     * @param UserUpdater $updater The updater to add
     */
    public function addUpdater($updater)
    {
        $this->updaters[] = $updater;
    }

    /**
     * Get the value of addresses
     *
     * @return Address[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set the value of addresses
     *
     * @param Address[] $addresses
     *
     * @return User
     */
    public function setAddresses(array $addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * Add an address to the user
     *
     * @param Address $address The address to add
     */
    public function addAddress($address)
    {
        $this->addresses[] = $address;
    }

    public function eraseCredentials()
    {

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

    public function getSalt()
    {

    }
}