<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SoftwareRepository")
 */
class Software implements \JsonSerializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $name;

    /**
     * current version of the software
     *
     * @ORM\Column(type="text", length=65536, nullable=true)
     */
    private $versions;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getVersions(): ?array
    {
        return json_decode($this->versions, true);
    }

    /**
     * @param array $versions
     * @return Software
     */
    public function setVersions(array $versions): self
    {
        $this->versions = json_encode($versions);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Software
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'versions' => $this->getVersions()
        ];
    }
}
