<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstallationRepository")
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"hash"})})
 */
class Installation implements \JsonSerializable
{

    const CONTAO_MANAGER = 'contao-manager.phar.php';

    /**
     * Installation constructor.
     */
    public function __construct()
    {
        $this->setAdded();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $hash;

    /**
     * last time where the /config endpoint was called
     *
     * @var \DateTime
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $lastUpdate;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetimetz_immutable", nullable=true)
     */
    private $added;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $cleanUrl;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $managerUrl;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=1023, nullable=true)
     */
    private $favicon;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $themeColor;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @return Installation
     */
    public function setLastUpdate(): self
    {
        try {
            $this->lastUpdate = new \DateTime();
        } catch (\Exception $e) {
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @return Installation
     */
    public function setAdded(): self
    {
        try {
            // can only be set once
            if (!$this->added) {
                $this->added = new \DateTimeImmutable();
            }
        } catch (\Exception $e) {
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Installation
     */
    public function setUrl($url): self
    {
        $this->url = $url;
        $this->hash = hash('sha256', $url);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCleanUrl()
    {
        return $this->cleanUrl;
    }

    /**
     * @param mixed $url
     * @return Installation
     */
    public function setCleanUrl($url): self
    {
        $this->cleanUrl = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagerUrl()
    {
        return $this->managerUrl;
    }

    /**
     * localhosts have no Contao Manager file
     *
     * @param mixed $url
     * @return Installation
     */
    public function setManagerUrl($url): self
    {
        $this->managerUrl = (strpos($url, 'localhost') !== false)
            ? $url
            : $url . '/' . static::CONTAO_MANAGER;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return Installation
     */
    public function setToken($token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * @param $favicon
     * @return Installation
     */
    public function setFavicon($favicon): self
    {
        $this->favicon = $favicon;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return Installation
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getThemeColor()
    {
        return $this->themeColor;
    }

    /**
     * @param mixed $themeColor
     */
    public function setThemeColor($themeColor): void
    {
        $this->themeColor = $themeColor;
    }



    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_merge(
            [
                'hash' => $this->hash,
                'url' => $this->url,
                'cleanUrl' => $this->cleanUrl,
                'managerUrl' => $this->managerUrl,
                'lastUpdate' => $this->lastUpdate ? $this->lastUpdate->format(DATE_ATOM) : null, // formatted for json
                'added' => $this->added ? $this->added->format(DATE_ATOM) : null,
                'favicon' => $this->favicon,
                'title' => $this->title,
                'themeColor' => $this->themeColor
            ]
        );

    }

    public function removeChildren(EntityManagerInterface $entityManager): void
    {
        $monitorings = $entityManager->getRepository(Monitoring::class)->findByInstallationId($this->getId());
        if ($monitorings) {
            foreach ($monitorings as $monitoring) {
                $entityManager->remove($monitoring);
            }
        }

        $task = $entityManager->getRepository(Task::class)->findOneByInstallation($this);
        if ($task) {
            $entityManager->remove($task);
        }
    }

}
