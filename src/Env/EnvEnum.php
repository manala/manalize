<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env;

use Manala\Exception\InvalidEnvException;

/**
 * Manala Env type.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class EnvEnum
{
    const SYMFONY = 'symfony';

    private $name;

    /**
     * @param string $name One of the existing envs
     *
     * @return EnvEnum
     */
    public static function create($name)
    {
        return new self($name);
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return bool Whether the env exists or not
     */
    public static function exists($name)
    {
        return in_array($name, self::getPossibleEnvs(), true);
    }

    /**
     * @return bool Whether this env is of the given type
     */
    public function is($name)
    {
        return $name === $this->name;
    }

    /**
     * @return array
     */
    public static function getPossibleEnvs()
    {
        return [
            self::SYMFONY,
        ];
    }

    final private function __construct($name)
    {
        if (false === self::exists($name)) {
            throw new InvalidEnvException($name);
        }

        $this->name = $name;
    }
}
