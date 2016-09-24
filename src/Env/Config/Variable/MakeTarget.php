<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env\Config\Variable;

/**
 * Manala "app" and "vendor" environment variables.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class MakeTarget implements Variable
{
    private $name;
    private $commands;

    /**
     * @param string $name
     */
    public function __construct($name, array $commands)
    {
        $this->name = $name;
        $this->commands = $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaces()
    {
        $replaces = '';

        for ($i = 0; $i < count($this->commands); ++$i) {
            $command = $this->commands[$i];
            $replaces .= 0 < $i ? "\n\t$command" : $command;
        }

        return [
            sprintf('{{ %s_tasks }}', $this->name) => $replaces,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function validate($value = null)
    {
    }
}
