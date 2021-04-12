<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * People is a class in which we storage information about a human being,
 * like their address, their phone number, etc.
 *
 * @ORM\Table(name="people")
 * @ORM\Entity(repositoryClass="App\Repository\PeopleRepository")
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
     * @var \App\Entity\User
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
     * Year of the first contact of the people
     *
     * @var int
     *
     * @ORM\Column(name="first_contact_year", type="integer")
     */
    private $firstContactYear;

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
     * @var boolean
     *
     * @ORM\Column(name="is_receiving_newsletter", type="boolean")
     */
    private $isReceivingNewsletter = false;

    /**
     * A boolean used to check if the people want to receive the newletter
     * by mail or by email (true for email, false for physical mail).
     *
     * @var boolean
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
     * @ORM\ManyToMany(targetEntity="App\Entity\PeopleType", inversedBy="peoples", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="peoples_people_types",
     *      joinColumns={@JoinColumn(name="people_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="people_type_id", referencedColumnName="id")}
     * )
     */
    private $types;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Membership", inversedBy="members", cascade={"persist", "remove"})
     * @ORM\OrderBy({"date_start" = "DESC"})
     * @ORM\JoinTable(
     *      name="peoples_memberships",
     *      joinColumns={@JoinColumn(name="membership_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="people_id", referencedColumnName="id")}
     * )
     */
    private $memberships;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Donation", mappedBy="donator", orphanRemoval=true)
     */
    private $donations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="payer", orphanRemoval=true)
     */
    private $payments;

    /**
     *
     */
    function __construct($id = -1, $user = NULL, $denomination = NULL, $firstName = NULL, $lastName = NULL)
    {
        $this->id = $id;
        $this->user = $user;
        $this->denomination = $denomination;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->memberships = new ArrayCollection();
        $this->donations = new ArrayCollection();
        $this->firstContactYear = (int) date('Y');
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
     * @param boolean $isReceivingNewsletter If the person receives the newsletter.
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
     * @param string $homePhoneNumber Home Number.
     *
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @param UserUpdater[] $updaters People that updated the profile.
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
     * @return User
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

    public function getActiveMembership()
    {
        foreach ($this->memberships as $membership)
        {
            if ($membership->getDateEnd() > new \DateTime("now"))
            {
                return $membership;
            }
        }
        return null;
    }

    function getTypes()
    {
        return $this->types;
    }

    function setTypes($types): void
    {
        $this->types = $types;
    }

    /**
     * Add a type to the person
     *
     * @param Type $type The type to add.
     */
    public function addType($type)
    {
        $this->types[] = $type;
    }

    /**
     * Remove a type from the person
     *
     * @param Type $typeToRemove The typte to remove.
     */
    public function removeType($typeToRemove)
    {
        $typeToRemoveIndex = null;

        if (empty($this->types))
        {
            return $this;
        }

        foreach ($this->types as $index => $type)
        {
            if ($type->getId() == $typeToRemove->getId())
            {
                $typeToRemoveIndex = $index;
                break;
            }
        }

        if ($typeToRemoveIndex !== null)
        {
            $this->types->remove($typeToRemoveIndex);
        }
        return $this;
    }

    public function getMemberships()
    {
        return $this->memberships;
    }

    public function setMemberships(?Membership $memberships): self
    {
        $this->memberships = $memberships;

        return $this;
    }

    /**
     * Add a membership to the person
     *
     * @param Membership $membership The membership to add.
     */
    public function addMembership($membership)
    {
        $this->memberships[] = $membership;
    }

    /**
     * Remove a membership from the person
     *
     * @param Membership $membershipToRemove The membership to remove.
     */
    public function removeMembership($membershipToRemove)
    {
        $membershipToRemoveIndex = null;

        foreach($this->memberships as $index => $membership)
        {
            if($membership->getId() == $membershipToRemove->getId())
            {
                $membershipToRemoveIndex = $index;
                break;
            }
        }

        if($membershipToRemoveIndex !== null)
        {
            $this->memberships->remove($membershipToRemoveIndex);
        }
    }

    /**
     * @return Collection|Donation[]
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): self
    {
        if (!$this->donations->contains($donation)) {
            $this->donations[] = $donation;
            $donation->setDonator($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): self
    {
        if ($this->donations->contains($donation)) {
            $this->donations->removeElement($donation);
            // set the owning side to null (unless already changed)
            if ($donation->getDonator() === $this) {
                $donation->setDonator(null);
            }
        }

        return $this;
    }

    /**
     * Check is this people has a Contact type
     *
     * @return bool True if people has Contact type, false otherwise
     */
    public function isContact(): bool
    {
        foreach ($this->getTypes() as $type)
        {
            if ($type->getCode() === PeopleType::CONTACT_CODE)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Check is this people has a Social Pole type
     *
     * @return bool True if people has Social Pole type, false otherwise
     */
    public function needHelp(): bool
    {
        foreach ($this->getTypes() as $type)
        {
            if ($type->getCode() === PeopleType::SOCIAL_POLE_CODE)
            {
                return true;
            }
        }
        return false;
    }
}
