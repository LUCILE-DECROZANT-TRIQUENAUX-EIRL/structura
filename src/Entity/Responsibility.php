<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsibility
 *
 * @ORM\Table(name="responsibility")
 * @ORM\Entity(repositoryClass="App\Repository\ResponsibilityRepository")
 */
class Responsibility
{
    const ADMINISTRATEURICE_LABEL = 'Administrateurice de la base de données';
    const ADMINISTRATEURICE_SENSIBLE_LABEL = 'Administrateurice des données sensibles';
    const GESTIONNAIRE_LABEL = 'Gestionnaire';
    const GESTIONNAIRE_SENSIBLE_LABEL = 'Gestionnaire des données sensibles';
    const MEMBER = 'Adhérent.e';
    const EX_MEMBER = 'Ex-adhérent.e';
    const INFORMATEURICE_LABEL = 'Informateurice';
    const SYMPATHIZE_LABEL = 'Sympathisant.e';
    const REGISTERED_LABEL = 'Inscrit.e';
    const CONSULTATION_ANNUAIRE_LABEL = 'Consultation de l\'annuaire';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Code of the responsibility used by Symfony to determine which role it is
     *
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * Name of the responsibility
     *
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    private $label;

    /**
     * Description of the responsibility
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * Determine if the responsibility is automatically managed
     * by the application or if it's managed by the users
     *
     * @var boolean
     *
     * @ORM\Column(name="automatic", type="boolean")
     */
    private $automatic;

    /**
     *
     */
    function __construct($id = -1, $code = NULL, $label = NULL, $description = NULL)
    {
        $this->id = $id;
        $this->code = $code;
        $this->label = $label;
        $this->description = $description;
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
     * Set label
     *
     * @param string $label Label of the responsibility.
     *
     * @return Responsibility
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get code
     *
     * @return string
     */
    function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code Code of the responsibility.
     * @return ResponsabXility
     */
    function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the responsibility description
     *
     * @return string
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the responsibility description
     *
     * @param string $description Description of the responsibility.
     */
    function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    function getAutomatic()
    {
        return $this->automatic;
    }

    /**
     * Check if the responsibility is automatically managed
     *
     * @return boolean True if it is, false otherwise
     */
    function isAutomatic()
    {
        return $this->getAutomatic();
    }

    /**
     * Set if the responsibility is automatically managed
     *
     * @param boolean $automatic True if it is, false otherwise
     */
    function setAutomatic(boolean $automatic)
    {
        $this->automatic = $automatic;
    }
}
