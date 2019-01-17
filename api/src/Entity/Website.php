<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebsiteRepository")
 */
class Website implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $repo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $managerUsername;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $managerPassword;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRepo(): ?string
    {
        return $this->repo;
    }

    public function setRepo(string $repo): self
    {
        $this->repo = $repo;

        return $this;
    }

    public function getManagerUsername(): ?string
    {
        return $this->managerUsername;
    }

    public function setManagerUsername(string $managerUsername): self
    {
        $this->managerUsername = $managerUsername;

        return $this;
    }

    public function getManagerPassword(): ?string
    {
        return $this->managerPassword;
    }

    public function setManagerPassword(string $managerPassword): self
    {
        $this->managerPassword = $managerPassword;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'repo' => $this->repo,
            'managerUsername' => $this->managerUsername,
            'managerPassword' => $this->managerPassword,
        ];
    }
}
