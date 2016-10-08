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
use Manala\Manalize\Env\Config\Variable\Variable;

/**
 * Manala Env.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Env
{
    /** @var string */
    private $name;

    private $configs = [];

    public function __construct($name, Config ...$configs)
    {
        $this->name = $name;
        $this->configs = $configs;
    }

    /**
     * @return Config[]
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    public function export()
    {
        $vars = [];
        foreach ($this->getVars() as $var) {
            foreach ($var->getReplaces() as $placeholder => $value) {
                $vars[$placeholder] = $value;
            }
        }

        return [
            'env' => $this->name,
            'vars' => $vars,
        ];
    }

    /**
     * @return Variable[]
     */
    private function getVars()
    {
        return array_reduce($this->getConfigs(), function ($previous, Config $config) {
            return array_merge($previous, $config->getVars());
        }, []);
    }
}
