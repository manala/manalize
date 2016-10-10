<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env;

use Manala\Manalize\Env\Config\Ansible;
use Manala\Manalize\Env\Config\Make;
use Manala\Manalize\Env\Config\Vagrant;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersionResolver;

/**
 * Provides Env instances.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class EnvFactory
{
    public static function createEnv(EnvEnum $type, AppName $appName, \Traversable $dependencies)
    {
        return new Env(
            (string) $type,
            new Vagrant($type, $appName, VagrantBoxVersionResolver::resolve($dependencies)),
            new Ansible($type, ...$dependencies),
            new Make($type)
        );
    }
}
