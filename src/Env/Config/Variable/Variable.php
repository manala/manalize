<?php

namespace Manala\Env\Config\Variable;

/**
 * Must be implemented by classes representing a variable to be rendered.
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
    public function getReplaces();

    /**
     * Checks that a given value is properly formatted for the current implementation.
     *
     * @param string $value The value to assert
     *
     * @return string The validated value
     *
     * @throws \InvalidArgumentException If the value is incorrect
     */
    public static function validate($value);
}
