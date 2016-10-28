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

/**
 * Manala Env.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Env
{
    private $name;
    private $configs = [];

    public function __construct(string $name, Config ...$configs)
    {
        $this->name = $name;
        $this->configs = $configs;
    }

    /**
     * @return Config[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }
}
