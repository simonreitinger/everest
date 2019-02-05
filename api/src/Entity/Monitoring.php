<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MonitoringRepository")
 * @ORM\Table(uniqueConstraints={
 *      @UniqueConstraint(name="monitoring_unique",
 *        columns={"website_id", "created_at"})
 * })
 */
class Monitoring implements \JsonSerializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTimeImmutable $createdAt
     * @ORM\Column(type="datetimetz_immutable")
     */
    private $createdAt;

    /**
     * @var Website
     * @ORM\ManyToOne(targetEntity="Website")
     * @ORM\JoinColumn(name="website_id", referencedColumnName="id")
     */
    private $website;

    /**
     * @ORM\Column(type="integer")
     */
    private $requestTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * Monitoring constructor.
     * @param $time
     */
    public function __construct()
    {
        $this->setCreatedAt();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setCreatedAt(): self
    {
        try {
            if (!$this->createdAt) {
                $this->createdAt = new \DateTimeImmutable();
            }
        } catch (\Exception $e) {
        }

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getRequestTime(): int
    {
        return $this->requestTime;
    }

    public function setRequestTime(int $requestTime): self
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'websiteHash' => $this->website->getHash(),
            'createdAt' => $this->createdAt->format(DATE_ATOM),
            'status' => $this->status,
            'statusText' => Response::$statusTexts[$this->status],
            'failed' => (bool)$this->status !== Response::HTTP_OK,
            'requestTimeInMs' => $this->requestTime
        ];
    }
}
