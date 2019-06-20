<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * People is a class in which we storage information about a human being,
 * like their address, their phone number, etc.
 *
 * @ORM\Table(name="people")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeopleRepository")
 */
class People
{

    /**
     * A private ID used to identify the people.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The user with which the people connect themselves to the application.
     *
     * @var \AppBundle\Entity\User
     *
     * @OneToOne(targetEntity="User", inversedBy="people")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * A title used to refer to the people (e.G. Doctor, Mx, etc.)
     *
     * @ORM\ManyToOne(targetEntity="Denomination")
     * @ORM\JoinColumn(name="denomination_id", referencedColumnName="id", nullable=true)
     */
    private $denomination;

    /**
     * The first name of the people.
     *
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * The last name of the people.
     *
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * The email address of the people.
     *
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=255, nullable=true)
     */
    private $emailAddress;

    /**
     * A boolean used to check if the people want to receive the newletter.
     *
     * @var bool
     *
     * @ORM\Column(name="is_receiving_newsletter", type="boolean")
     */
    private $isReceivingNewsletter = false;

    /**
     * A boolean used to check if the people want to receive the newletter
     * by mail or by email (true for email, false for physical mail).
     *
     * @var bool
     *
     * @ORM\Column(name="newsletter_dematerialization", type="boolean")
     */
    private $newsletterDematerialization = false;

    /**
     * The home phone number of the people.
     *
     * @var string
     *
     * @ORM\Column(name="home_phone_number", type="string", length=10, nullable=true)
     */
    private $homePhoneNumber;

    /**
     * The cell phone number of the people.
     *
     * @var string
     *
     * @ORM\Column(name="cell_phone_number", type="string", length=10, nullable=true)
     */
    private $cellPhoneNumber;

    /**
     * The work phone number of the people.
     *
     * @var string
     *
     * @ORM\Column(name="work_phone_number", type="string", length=10, nullable=true)
     */
    private $workPhoneNumber;

    /**
     * The fax number of the people.
     *
     * @var string
     *
     * @ORM\Column(name="work_fax_number", type="string", length=10, nullable=true)
     */
    private $workFaxNumber;

    /**
     * A big field used to store comments about the people.
     *
     * @var string
     *
     * @ORM\Column(name="observations", type="string", length=1000, nullable=true)
     */
    private $observations;

    /**
     * A big field used to store sensitive comments about the people.
     * It can only be accessed by users having the right responsabilities.
     *
     * @var string
     *
     * @ORM\Column(name="sensitive_observations", type="string", length=1000, nullable=true)
     */
    private $sensitiveObservations;

    /**
     * The list of all the update of the people.
     *
     * @var UserUpdater[]
     *
     * @ORM\OneToMany(targetEntity="UserUpdater", mappedBy="updater")
     */
    private $updaters;

    /**
     * The different addresses of the people.
     *
     * @var Address[]
     *
     * @ORM\ManyToMany(targetEntity="Address")
     * @ORM\JoinTable(
     *      name="people_addresses",
     *      joinColumns={@JoinColumn(name="people_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="address_id", referencedColumnName="id")}
     * )
     */
    private $addresses;

    /**
     *
     */
    function __construct()
    {
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

    /**
     * Return the User corresponding to this people, if exists.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the User corresponding to this people
     *
     * @param \AppBundle\Entity\User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Get the value of sensitive observations
     *
     * @return string
     */
    public function getSensitiveObservations()
    {
        return $this->sensitiveObservations;
    }

    /**
     * Set the value of sensitive observations
     *
     * @param string $sensitiveObservations
     *
     * @return User
     */
    public function setSensitiveObservations(string $sensitiveObservations)
    {
        $this->sensitiveObservations = $sensitiveObservations;

        return $this;
    }
}