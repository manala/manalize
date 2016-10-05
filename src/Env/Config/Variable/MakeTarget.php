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
    /** @var string */
    private $name;

    /** @var string[] */
    private $commands;

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
        return [
            sprintf('{{ %s_tasks }}', $this->name) => array_reduce($this->commands, function ($previous, $command) {
                return $previous === null ? "$command" : "$previous\n\t$command";
            }),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function validate($value)
    {
    }
}
