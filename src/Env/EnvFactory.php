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
use Manala\Manalize\Env\Config\Variable\Package;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use function iter\toArray;

/**
 * Provides Env instances.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class EnvFactory
{
    public static function createEnv(EnvName $name, AppName $appName, $packages): Env
    {
        if ($packages instanceof \Traversable) {
            $packages = toArray($packages);
        }

        return new Env(
            $name->getValue(),
            $appName,
            $packages,
            new Vagrant($name, $appName, new VagrantBoxVersion('~> 3.0.0')),
            Ansible::create($name, $packages),
            new Make($name)
        );
    }

    /**
     * @param array $manala The parsed manala.yaml e.g:
     *
     *   app:
     *      name: foo.bar
     *      template: elao-symfony
     *   system:
     *      redis: true        # equals { enabled: true, version: ~ }
     *      java: ~            # equals { enabled: true, version: ~ }
     *      php:
     *          version: 7.1
     *          extensions: []
     *      cpus: 1
     *      memory: 2048
     *
     * @return Env
     */
    public static function createEnvFromManala(array $manala, string $template = null): Env
    {
        $app = $manala['app'] ?? [];
        $system = $manala['system'] ?? [];

        if (!$template) {
            $template = $app['template'] ?? EnvName::CUSTOM;
        }

        $packages = [];
        foreach ($system as $name => $package) {
            $enabled = is_bool($package) ? $package : $package['enabled'] ?? (bool) $package;
            $version = is_string($package) ? $package : $package['version'] ?? null;
            // TODO manifest-based validation
            $packages[] = new Package($name, $enabled, $version);
        }

        return self::createEnv(EnvName::get($template), new AppName($app['name']), $packages);
    }
}
