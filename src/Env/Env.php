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

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Config\Manifest;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Package;

/**
 * Manala Env.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Env
{
    private $name;
    private $appName;
    private $configs = [];
    private $packages;

    /**
     * @param string    $name
     * @param Package[] $packages
     * @param Config[]  ...$configs
     */
    public function __construct(string $name, AppName $appName, array $packages, Config ...$configs)
    {
        $this->name = $name;
        $this->appName = $appName;
        $this->packages = $packages;
        $this->configs = $configs;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Config[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getBaseDir(): \SplFileInfo
    {
        return new \SplFileInfo(MANALIZE_HOME.'/templates/'.$this->name);
    }

    public function getAppName(): AppName
    {
        return $this->appName;
    }

    /**
     * @return Package[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
