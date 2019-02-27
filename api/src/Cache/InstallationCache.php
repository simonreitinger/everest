<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-27
 * Time: 15:31
 */

namespace App\Cache;

use App\Entity\Installation;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class InstallationCache
{
    /**
     * @var CacheInterface $cache
     */
    private $cache;

    /**
     * InstallationCache constructor.
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function saveInCache(Installation $installation, InstallationData $data)
    {
        try {
            $this->cache->set($installation->getCleanUrl(), json_encode($data), 3600);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }

    public function findByInstallation(Installation $installation)
    {
        try {
            return $this->cache->get($installation->getCleanUrl());
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
