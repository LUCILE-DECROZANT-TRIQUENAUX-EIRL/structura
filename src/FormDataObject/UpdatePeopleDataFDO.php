<?php
namespace App\FormDataObject;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\People;
use App\Entity\PeopleType;

class UpdatePeopleDataFDO
{

    /**
     * A private ID used to identify the people.
     * @var int
     */
    private $id;

    /**
     * A title used to refer to the people (e.G. Doctor, Mx, etc.)
     * @var Denomination
     */
    private $denomination;

    /**
     * The first name of the people.
     * @var string
     */
    private $firstName;

    /**
     * The last name of the people.
     * @var string
     */
    private $lastName;

    /**
     * Year of the first contact of the people
     *
     * @var int
     */
    private $firstContactYear;

    /**
     * The email address of the people.
     * @var string
     */
    private $emailAddress;

    /**
     * A boolean used to check if the people want to receive the newletter.
     * @var boolean
     */
    private $isReceivingNewsletter = false;

    /**
     * A boolean used to check if the people has PeopleType contact.
     * @var boolean
     */
    private $isContact = false;

    /**
     * A boolean used to check if the people has PeopleType social pole.
     * @var boolean
     */
    private $needHelp = false;

    /**
     * A boolean used to check if the people want to receive the newletter
     * by mail or by email (true for email, false for physical mail).
     * @var boolean
     */
    private $newsletterDematerialization = false;

    /**
     * The home phone number of the people.
     * @var string
     */
    private $homePhoneNumber;

    /**
     * The cell phone number of the people.
     * @var string
     */
    private $cellPhoneNumber;

    /**
     * The work phone number of the people.
     * @var string
     */
    private $workPhoneNumber;

    /**
     * The fax number of the people.
     * @var string
     */
    private $workFaxNumber;

    /**
     * A big field used to store comments about the people.
     * @var string
     */
    private $observations;

    /**
     * A big field used to store sensitive comments about the people.
     * It can only be accessed by users having the right responsabilities.
     * @var string
     */
    private $sensitiveObservations;

    /**
     * The list of all the update of the people.
     * @var UserUpdater[]
     */
    private $updaters;

    /**
     * The different addresses of the people.
     * @var Address[]
     */
    private $addresses;

    function __construct()
    {
        $this->firstContactYear = (int) date('Y');
    }

    /**
     *
     */
    public static function fromPeople(People $people): self
    {
        $updatePeopleDataFDO = new self();

        //$updatePeopleDataFDO->id = $people->getId();
        $updatePeopleDataFDO->denomination = $people->getDenomination();
        $updatePeopleDataFDO->firstName = $people->getFirstName();
        $updatePeopleDataFDO->lastName = $people->getLastName();
        if (empty($people->getFirstContactYear()))
        {
            $updatePeopleDataFDO->firstContactYear = (int) date('Y');
        }
        else
        {
            $updatePeopleDataFDO->firstContactYear = $people->getFirstContactYear();
        }
        $updatePeopleDataFDO->isContact = $people->isContact();
        $updatePeopleDataFDO->needHelp = $people->needHelp();
        $updatePeopleDataFDO->emailAddress = $people->getEmailAddress();
        $updatePeopleDataFDO->isReceivingNewsletter = $people->getIsReceivingNewsletter();
        $updatePeopleDataFDO->newsletterDematerialization = $people->getNewsletterDematerialization();
        $updatePeopleDataFDO->cellPhoneNumber = $people->getCellPhoneNumber();
        $updatePeopleDataFDO->homePhoneNumber = $people->getHomePhoneNumber();
        $updatePeopleDataFDO->workFaxNumber = $people->getWorkFaxNumber();
        $updatePeopleDataFDO->workPhoneNumber = $people->getWorkPhoneNumber();
        $updatePeopleDataFDO->observations = $people->getObservations();
        $updatePeopleDataFDO->sensitiveObservations = $people->getSensitiveObservations();
        $updatePeopleDataFDO->addresses = $people->getAddresses();

        return $updatePeopleDataFDO;
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
     * @return Denomination
     */
    public function getDenomination()
    {
        return $this->denomination;
    }

    /**
     * Set the value of denomination
     * @param Denomination
     *
     * @return UpdatePeopleDataFDO
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
     * @return UpdatePeopleDataFDO
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
     * @return UpdatePeopleDataFDO
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return int
     */
    function getFirstContactYear(): int
    {
        return $this->firstContactYear;
    }

    /**
     * @param int $firstContactYear
     * @return \self
     */
    function setFirstContactYear(int $firstContactYear): self
    {
        $this->firstContactYear = $firstContactYear;
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
     * @return UpdatePeopleDataFDO
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
     * @param boolean $isReceivingNewsletter If the person receives the newsletter.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setIsReceivingNewsletter(bool $isReceivingNewsletter)
    {
        $this->isReceivingNewsletter = $isReceivingNewsletter;

        return $this;
    }

    function isContact(): ?bool
    {
        return $this->isContact;
    }

    function setIsContact(bool $isContact): self
    {
        $this->isContact = $isContact;
        return $this;
    }

    function needHelp(): ?bool
    {
        return $this->needHelp;
    }

    function setNeedHelp(bool $needHelp): self
    {
        $this->needHelp = $needHelp;
        return $this;
    }

    /**
     * Get the value of newsletterDematerialization
     *
     * @return boolean
     */
    public function getNewsletterDematerialization()
    {
        return $this->newsletterDematerialization;
    }

    /**
     * Set the value of newsletterDematerialization
     *
     * @param boolean $newsletterDematerialization Is the newsletter received by mail.
     *
     * @return UpdatePeopleDataFDO
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
     * @param string $homePhoneNumber Home Number.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setHomePhoneNumber(string $homePhoneNumber)
    {
        if (!is_numeric($homePhoneNumber))
        {
            throw new \InvalidArgumentException('The input given is not a phone number, only numbers allowed');
        }
        else
        {
            $this->homePhoneNumber = $homePhoneNumber;
            return $this;
        }
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
     * @param string $cellPhoneNumber Cell Number.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setCellPhoneNumber(string $cellPhoneNumber)
    {
        if (!is_numeric($cellPhoneNumber))
        {
            throw new \InvalidArgumentException('The input given is not a phone number, only numbers allowed');
        }
        else
        {
            $this->cellPhoneNumber = $cellPhoneNumber;
            return $this;
        }
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
     * @param string $workPhoneNumber Work Number.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setWorkPhoneNumber(string $workPhoneNumber)
    {
        if (!is_numeric($workPhoneNumber))
        {
            throw new \InvalidArgumentException('The input given is not a phone number, only numbers allowed');
        }
        else
        {
            $this->workPhoneNumber = $workPhoneNumber;
            return $this;
        }
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
     * @param string $workFaxNumber Fax Number.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setWorkFaxNumber(string $workFaxNumber)
    {
        if (!is_numeric($workFaxNumber))
        {
            throw new \InvalidArgumentException('The input given is not a phone number, only numbers allowed');
        }
        else
        {
            $this->workFaxNumber = $workFaxNumber;
            return $this;
        }

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
     * @param string $observations Observations on the person.
     *
     * @return UpdatePeopleDataFDO
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
     * @param UserUpdater[] $updaters People that updated the profile.
     *
     * @return UpdatePeopleDataFDO
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
     * @param Address[] $addresses Adresses of the person.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setAddresses(array $addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * Add an address to the user
     *
     * @param Address $address The address to add.
     */
    public function addAddress($address)
    {
        $this->addresses[] = $address;
    }

    /**
     * Return the User corresponding to this people, if exists.
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the User corresponding to this people
     *
     * @param \App\Entity\User $user
     * @return UpdatePeopleDataFDO
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
     * @param string $sensitiveObservations Sensitve observations.
     *
     * @return UpdatePeopleDataFDO
     */
    public function setSensitiveObservations(string $sensitiveObservations)
    {
        $this->sensitiveObservations = $sensitiveObservations;

        return $this;
    }

    public function hasNoAddressDefined()
    {
        $emptyAddress = new Address();
        $addresses = $this->getAddresses();
        foreach ($addresses as $address)
        {
            if (
                    $address->getLine() !== $emptyAddress->getLine() ||
                    $address->getPostalCode() !== $emptyAddress->getPostalCode() ||
                    $address->getCity() !== $emptyAddress->getCity() ||
                    $address->getCountry() !== $emptyAddress->getCountry()
                )
            {
                return false;
            }
        }
        return true;
    }
}
