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

use App\Manager\Version\VersionManagerInterface;

class VersionManagerFactory
{
    /**
     * naming convention: first letter uppercase, everything else lowercase
     * e.g. key "php" becomes "PhpVersionManager".
     *
     * @param $name
     *
     * @throws \Exception
     *
     * @return VersionManagerInterface
     */
    public static function create($name)
    {
        $class = sprintf('App\Manager\Version\%sVersionManager', ucfirst($name));

        if (!class_exists($class)) {
            throw new \Exception($class.' was not found');
        }

        return new $class();
    }
}
