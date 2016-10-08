<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config\Variable;

use Manala\Manalize\Env\Config\Variable\Dependency\Dependency;
use Manala\Manalize\Env\Config\Variable\Dependency\VersionBounded;
use function iter\search;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class VagrantBoxVersionResolver
{
    const DEFAULT_BOX_VERSION = '~> 3.0.0';

    /**
     * Resolves the vagrant box version.
     *
     * @param \Iterator $dependencies
     *
     * @return VagrantBoxVersion
     */
    public static function resolve(\Iterator $dependencies)
    {
        $php = self::getDependency('php', $dependencies);

        if (null === $php) {
            return new VagrantBoxVersion(self::DEFAULT_BOX_VERSION);
        }

        return new VagrantBoxVersion(self::resolveFromPhpVersion($php->getVersion()));
    }

    /**
     * @return string
     */
    private static function resolveFromPhpVersion($phpVersion)
    {
        return (float) $phpVersion < 7 ? '~> 2.0.0' : self::DEFAULT_BOX_VERSION;
    }

    /**
     * @param string   $name
     * @param Iterator $dependencies
     *
     * @return VersionBounded|false
     */
    private static function getDependency($name, \Iterator $dependencies)
    {
        return search(function (Dependency $dependency) {
            return $dependency instanceof VersionBounded && 'php' === $dependency->getName();
        }, $dependencies);
    }
}
