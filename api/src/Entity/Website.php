<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebsiteRepository")
 */
class Website implements \JsonSerializable
{
    const CONTAO_MANAGER = 'contao-manager.phar.php';

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
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $added;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cleanUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $managerUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=1023, nullable=true)
     */
    private $favicon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * from /api/server/contao
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $contao;

    /**
     * from /api/server/composer
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $composer;

    /**
     * from /api/config/manager
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $manager;

    /**
     * from /api/server/php-cli
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $phpCli;

    /**
     * from /api/server/php-web
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $phpWeb;

    /**
     * from /api/server/config
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $config;

    /**
     * from /api/server/self-update
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $selfUpdate;

    /**
     * from /api/packages/root
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $packages;

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
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @return Website
     */
    public function setLastUpdate(): Website
    {
        $this->lastUpdate = new \DateTime();

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
     * @return Website
     */
    public function setAdded(): Website
    {
        // can only be set once
        if (!$this->added) {
            $this->added = new \DateTime();
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
     * @return Website
     */
    public function setUrl($url): Website
    {
        $this->url = $url;

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
     * @return Website
     */
    public function setCleanUrl($url): Website
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
     * @return Website
     */
    public function setManagerUrl($url): Website
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
     * @return Website
     */
    public function setToken($token): Website
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
     * @return Website
     */
    public function setFavicon($favicon): Website
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
     * @return Website
     */
    public function setTitle($title): Website
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContao()
    {
        return $this->contao;
    }

    /**
     * @param mixed $contao
     * @return Website
     */
    public function setContao($contao): Website
    {
        $this->contao = $contao;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComposer()
    {
        return $this->composer;
    }

    /**
     * @param mixed $composer
     * @return Website
     */
    public function setComposer($composer): Website
    {
        $this->composer = $composer;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     * @return Website
     */
    public function setManager($manager): Website
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhpCli()
    {
        return $this->phpCli;
    }

    /**
     * @param mixed $phpCli
     * @return Website
     */
    public function setPhpCli($phpCli): Website
    {
        $this->phpCli = $phpCli;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhpWeb()
    {
        return $this->phpWeb;
    }

    /**
     * @param mixed $phpWeb
     * @return Website
     */
    public function setPhpWeb($phpWeb): Website
    {
        $this->phpWeb = $phpWeb;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     * @return Website
     */
    public function setConfig($config): Website
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSelfUpdate()
    {
        return $this->selfUpdate;
    }

    /**
     * @param mixed $selfUpdate
     * @return Website
     */
    public function setSelfUpdate($selfUpdate): Website
    {
        $this->selfUpdate = $selfUpdate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param mixed $packages
     */
    public function setPackages($packages): void
    {
        $this->packages = $packages;
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
            'id' => $this->id,
            'url' => $this->url,
            'cleanUrl' => $this->cleanUrl,
            'managerUrl' => $this->managerUrl,
            'lastUpdate' => $this->lastUpdate ? $this->lastUpdate->format(DATE_ATOM) : null, // formatted for json
            'added' => $this->added ? $this->added->format(DATE_ATOM) : null,
            'favicon' => $this->favicon,
            'title' => $this->title,
            'contao' => $this->contao,
            'composer' => $this->composer,
            'manager' => $this->manager,
            'phpCli' => $this->phpCli,
            'phpWeb' => $this->phpWeb,
            'config' => $this->config,
            'selfUpdate' => $this->selfUpdate,
            'packages' => $this->packages,
        ];
    }

}
