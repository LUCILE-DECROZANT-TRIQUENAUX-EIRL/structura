<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssociationRepository")
 */
class Association
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $logoFilename;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $treasurerSignatureFilename;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    public function setLogoFilename(?string $logoFilename): self
    {
        $this->logoFilename = $logoFilename;

        return $this;
    }

    public function getTreasurerSignatureFilename(): ?string
    {
        return $this->treasurerSignatureFilename;
    }

    public function setTreasurerSignatureFilename(?string $treasurerSignatureFilename): self
    {
        $this->treasurerSignatureFilename = $treasurerSignatureFilename;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
