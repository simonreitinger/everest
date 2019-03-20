<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Cache;

use App\Entity\Installation;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class InstallationCache
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * InstallationCache constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function saveInCache(Installation $installation, InstallationData $data): bool
    {
        try {
            $this->cache->set($installation->getCleanUrl(), json_encode($data));
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    public function findByInstallation(Installation $installation): ?string
    {
        return $this->cache->get($installation->getCleanUrl());
    }
}
