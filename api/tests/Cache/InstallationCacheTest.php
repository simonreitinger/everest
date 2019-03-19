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
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class InstallationCacheTest extends TestCase
{
    private $installation;
    private $installationData;
    private $cache;

    // only data is set where dependencies do not matter
    private $data = '{"contao":null,"composer":"{\"json\":{\"found\":true,\"valid\":true,\"error\":null}","manager":null,"phpCli":null,"phpWeb":null,"config":null,"composerLock":"{\"found\":true,\"fresh\":true}","selfUpdate":null,"packages":null}';

    protected function setUp(): void
    {
        parent::setUp();

        $this->installation = new Installation();
        $this->installation->setCleanUrl('contao.test');

        $this->installationData = new InstallationData();
        $this->installationData->setComposer('{"json":{"found":true,"valid":true,"error":null}');
        $this->installationData->setLock('{"found":true,"fresh":true}');

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn(json_encode($this->installationData));

        /* @var InstallationCache */
        $this->cache = new InstallationCache($cache);
    }

    public function testSaveInCache(): void
    {
        $this->assertTrue($this->cache->saveInCache($this->installation, $this->installationData));
    }

    /**
     * @depends testSaveInCache
     *
     * @param $data
     */
    public function testFindByInstallation($data): void
    {
        $this->assertSame($this->cache->findByInstallation($this->installation), $this->data);
    }
}
