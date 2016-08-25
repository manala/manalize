<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Config;

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
     * @return string
     */
    public function getDatabase()
    {
        return ($this->vendor ? $this->vendor.'_' : '').$this->app;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->app.($this->vendor ? '.'.$this->vendor : '');
    }
}
