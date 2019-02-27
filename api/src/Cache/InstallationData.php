<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-27
 * Time: 16:11
 */

namespace App\Cache;

class InstallationData implements \JsonSerializable
{
    /**
     * from /api/server/contao
     */
    private $contao;

    /**
     * from /api/server/composer
     */
    private $composer;

    /**
     * from /api/config/manager
     */
    private $manager;

    /**
     * from /api/server/php-cli
     */
    private $phpCli;

    /**
     * from /api/server/php-web
     */
    private $phpWeb;

    /**
     * from /api/server/config
     */
    private $config;

    /**
     * from /api/packages/local
     */
    private $composerLock;

    /**
     * from /api/server/self-update
     */
    private $selfUpdate;

    /**
     * from /api/packages/root
     */
    private $packages;

    /**
     * @return mixed
     */
    public function getContao()
    {
        return $this->contao;
    }

    /**
     * @param $contao
     * @return InstallationCache
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
     * @param $composer
     * @return InstallationCache
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
     * @param $manager
     * @return InstallationCache
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
     * @param $phpCli
     * @return InstallationCache
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
     * @param $phpWeb
     * @return InstallationCache
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
     * @param $config
     * @return InstallationCache
     */
    public function setConfig($config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLock()
    {
        return $this->composerLock;
    }

    /**
     * @param string $lock
     * @return InstallationCache
     */
    public function setLock($lock): self
    {
        $this->composerLock = $lock;

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
     * @return InstallationCache
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
     * @return InstallationCache
     */
    public function setPackages($packages): self
    {
        $this->packages = $packages;

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
            'contao' => $this->contao,
            'composer' => $this->composer,
            'manager' => $this->manager,
            'phpCli' => $this->phpCli,
            'phpWeb' => $this->phpWeb,
            'config' => $this->config,
            'composerLock' => $this->composerLock,
            'selfUpdate' => $this->selfUpdate,
            'packages' => $this->packages,
        ];
    }
}
