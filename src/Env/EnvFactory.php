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
use Manala\Manalize\Env\Config\Registry;
use Manala\Manalize\Env\Config\Vagrant;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersionResolver;
use Manala\Manalize\Env\Config\Variable\VariableHydrator;

/**
 * Provides Env instances.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class EnvFactory
{
    public static function createEnv(EnvName $name, AppName $appName, \Traversable $dependencies): Env
    {
        return new Env(
            $name->getValue(),
            new Vagrant($name, $appName, VagrantBoxVersionResolver::resolve($dependencies)),
            new Ansible($name, ...$dependencies),
            new Make($name)
        );
    }

    public static function createEnvFromMetadata(array $metadata): Env
    {
        $hydrator = new VariableHydrator();
        $configRegistry = new Registry();
        $rawName = $metadata['name'];
        $rawConfigs = $metadata['configs'];
        $name = EnvName::get($rawName);
        $configs = [];

        foreach ($rawConfigs as $configAlias => $perAliasVars) {
            $hydratedVars = [];
            $configClass = $configRegistry->getClassForAlias($configAlias);

            foreach ($perAliasVars as $alias => $vars) {
                $class = $configRegistry->getClassForAlias($alias);
                foreach ($vars as $data) {
                    $var = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
                    $hydrator->hydrate($var, $data);
                    $hydratedVars[] = $var;
                }
            }

            $configs[] = $configClass::create($name, $hydratedVars);
        }

        return new Env($rawName, ...$configs);
    }
}
