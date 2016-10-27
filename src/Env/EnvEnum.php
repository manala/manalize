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

use Manala\Manalize\Exception\InvalidEnvException;

/**
 * Manala Env type.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class EnvEnum
{
    const SYMFONY = 'symfony';

    private $name;

    public static function create(string $name) : self
    {
        return new self($name);
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function exists(string $name) : bool
    {
        return in_array($name, self::getPossibleEnvs(), true);
    }

    public function is(string $name) : bool
    {
        return $name === $this->name;
    }

    public static function getPossibleEnvs() : array
    {
        return [
            self::SYMFONY,
        ];
    }

    final private function __construct(string $name)
    {
        if (false === self::exists($name)) {
            throw new InvalidEnvException($name);
        }

        $this->name = $name;
    }
}
