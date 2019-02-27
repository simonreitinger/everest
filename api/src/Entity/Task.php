<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task implements \JsonSerializable
{
    const RUNNING = 'running';
    const DONE = 'done';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Installation
     * @ORM\OneToOne(targetEntity="Installation")
     * @ORM\JoinColumn(name="installation_id", referencedColumnName="id")
     */
    private $installation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * only a field for configuration
     */
    private $config;

    /**
     * @ORM\Column(type="text", length=65536, nullable=true)
     */
    private $output;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $createdAt;

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

    public function getInstallation()
    {
        return $this->installation;
    }

    public function setInstallation($installation): self
    {
        $this->installation = $installation;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param mixed $output
     * @return Task
     */
    public function setOutput($output)
    {
        $this->output = $output;

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
            'output' => json_decode($this->output, true),
            'installation' => $this->installation->getCleanUrl()
        ];
    }
}
