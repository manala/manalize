<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config;

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Dependency\Dependency;
use Manala\Manalize\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use Manala\Manalize\Env\Config\Variable\Variable;

/**
 * References Config and Variable classes.
 */
class Registry
{
    /**
     * Gets all possible {@link Config} and {@link Variable} implementations classes with
     * their alias as keys.
     *
     * @return array Of format [alias => FQCN]
     */
    public function getClassesByAliases(): array
    {
        return [
            // Config
            'make' => Make::class,
            'ansible' => Ansible::class,
            'vagrant' => Vagrant::class,
            'gitignore' => Gitignore::class,
            // Variable
            'app_name' => AppName::class,
            'box_version' => VagrantBoxVersion::class,
            'dependency' => Dependency::class,
            'dependency_with_version' => VersionBounded::class,
        ];
    }

    /**
     * Gets all possible {@link Config} and {@link Variable} implementations aliases with
     * their FQCN as keys.
     *
     * @return array Of format [FQCN => alias]
     */
    public function getAliasesByClasses(): array
    {
        return array_flip($this->getClassesByAliases());
    }

    /**
     * Gets the class name for a given alias.
     *
     * @return string
     *
     * @throws \InvalidArgumentException If the alias is not registered
     */
    public function getClassForAlias(string $alias): string
    {
        $classes = $this->getClassesByAliases();

        if (!isset($classes[$alias])) {
            throw new \InvalidArgumentException(sprintf('Alias "%s" is not referenced.', $alias));
        }

        return $classes[$alias];
    }

    /**
     * Gets the alias for a given class name.
     *
     * @return string
     *
     * @throws \InvalidArgumentException If the class is not registered
     */
    public function getAliasForClass(string $class): string
    {
        $aliases = $this->getAliasesByClasses();

        if (!isset($aliases[$class])) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not referenced.', $class));
        }

        return $aliases[$class];
    }
}
