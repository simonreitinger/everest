<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Factory;

use App\Manager\Version\PhpVersionManager;
use PHPUnit\Framework\TestCase;

class VersionManagerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $manager = VersionManagerFactory::create('php');

        $this->assertSame(\get_class($manager), PhpVersionManager::class);
    }
}
