<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config;

/**
 * Manala environment variables.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Vars
{
    private $vendor;
    private $app;

    /**
     * @param string $vendor
     * @param string $app
     */
    public function __construct($vendor, $app = 'app')
    {
        $this->vendor = strtolower($vendor);
        $this->app = strtolower($app);
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Checks that a given configuration value is properly formatted.
     *
     * @param string $value The value to assert
     *
     * @return string The validated value
     *
     * @throws \InvalidArgumentException If the value is incorrect
     */
    public static function validate($value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \InvalidArgumentException(sprintf(
                'This value must contain only alphanumeric characters and hyphens.',
                $value
            ));
        }

        return $value;
    }
}
