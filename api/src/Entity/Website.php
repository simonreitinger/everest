<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebsiteRepository")
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"hash"})})
 */
class Website implements \JsonSerializable
{

    const CONTAO_MANAGER = 'contao-manager.phar.php';

    /**
     * Website constructor.
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
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $themeColor;

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
     * @return Website
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
     * @return Website
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
     * @return Website
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
     * @return Website
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
     * @return Website
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
     * @return Website
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
     * @return Website
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
     * @return Website
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
    public function setContao($contao): self
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
    public function setComposer($composer): self
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
    public function setManager($manager): self
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
    public function setPhpCli($phpCli): self
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
    public function setPhpWeb($phpWeb): self
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
    public function setConfig($config): self
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
    public function setSelfUpdate($selfUpdate): self
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
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'hash' => $this->hash,
            'url' => $this->url,
            'cleanUrl' => $this->cleanUrl,
            'managerUrl' => $this->managerUrl,
            'lastUpdate' => $this->lastUpdate ? $this->lastUpdate->format(DATE_ATOM) : null, // formatted for json
            'added' => $this->added ? $this->added->format(DATE_ATOM) : null,
            'favicon' => $this->favicon,
            'title' => $this->title,
            'themeColor' => $this->themeColor,
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
