<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-24
 * Time: 16:01
 */

namespace App\Factory;

use App\Manager\Version\VersionManagerInterface;

class VersionManagerFactory
{

    /**
     * naming convention: first letter uppercase, everything else lowercase
     * e.g. key "php" becomes "PhpVersionManager"
     *
     * @param $name
     * @return VersionManagerInterface
     * @throws \Exception
     */
    public static function create($name)
    {
        $class = sprintf('App\Manager\Version\%sVersionManager', ucfirst($name));

        if (!class_exists($class)) {
            throw new \Exception($class . ' was not found');
        }

        return new $class;
    }
}
