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
 * Must be implemented by classes representing a variable to be replaced when
 * rendering a given config template.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
interface Variable
{
    /**
     * Returns the replaces to be used for rendering a config file.
     *
     * @return array Of format [placeholder => value]
     */
    public function getReplaces(): array;

    /**
     * Checks that a given value is properly formatted for the current implementation.
     *
     * @param string $value The value to assert
     *
     * @return mixed The validated value
     *
     * @throws \InvalidArgumentException If the value is incorrect
     */
    public static function validate(string $value);
}
