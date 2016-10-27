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
     * @param \Traversable $dependencies
     *
     * @return VagrantBoxVersion
     */
    public static function resolve(\Traversable $dependencies) : VagrantBoxVersion
    {
        $php = self::findDependency('php', $dependencies);

        if (!$php instanceof VersionBounded) {
            return new VagrantBoxVersion(self::DEFAULT_BOX_VERSION);
        }

        return new VagrantBoxVersion(self::resolveFromPhpVersion($php->getVersion()));
    }

    private static function resolveFromPhpVersion($phpVersion) : string
    {
        return (float) $phpVersion < 7 ? '~> 2.0.0' : self::DEFAULT_BOX_VERSION;
    }

    private static function findDependency(string $name, \Traversable $dependencies)
    {
        return search(function (Dependency $dependency) use ($name) {
            return $name === $dependency->getName();
        }, $dependencies);
    }
}
