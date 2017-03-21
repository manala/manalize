<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config\Variable;

/**
 * A simple key-value variable.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Variable
{
    private $name;
    private $value;

    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getReplaces(): array
    {
        return [$this->name => $this->value];
    }

    public static function validate($value)
    {
        return $value;
    }
}
