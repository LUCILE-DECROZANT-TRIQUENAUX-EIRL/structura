<?php
/**
 * Entity thet represents the Address
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="line", type="string", length=1000, nullable=true)
     */
    private $line;

    /**
     * @var string
     *
     * @ORM\Column(name="line_two", type="string", length=1000, nullable=true)
     */
    private $lineTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=5, nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * Class constructor
     * @param string $line Line of the address.
     * @param string $postalCode Postal code.
     * @param string $city The city.
     * @param string $country Country.
     */
    public function __construct(string $line = NULL, string $lineTwo = NULL, string $postalCode = NULL, string $city = NULL, string $country = NULL)
    {
        $this->line = $line;
        $this->lineTwo = $lineTwo;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set line
     *
     * @param string $line Line of the address.
     *
     * @return Address
     */
    public function setLine(?string $line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set line two
     *
     * @param string $lineTwo Second line of the address.
     *
     * @return Address
     */
    public function setLineTwo(?string $lineTwo)
    {
        $this->lineTwo = $lineTwo;

        return $this;
    }

    /**
     * Get line two
     *
     * @return string
     */
    public function getLineTwo()
    {
        return $this->lineTwo;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode Postal code.
     *
     * @return Address
     */
    public function setPostalCode(?string $postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        if (strlen($this->postalCode) == 4)
        {
            $this->postalCode = '0' . $this->postalCode;
        }
        return $this->postalCode;
    }

    /**
     * Set city
     *
     * @param string $city The city.
     *
     * @return Address
     */
    public function setCity(?string $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country Country.
     *
     * @return Address
     */
    public function setCountry(?string $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function isEmpty(): bool {
        return $this->line == null
            && $this->lineTwo == null
            && $this->postalCode == null
            && $this->city == null
            && $this->country == null;
    }
}
