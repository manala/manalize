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
 * Represents a config part of a Manala environment.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
abstract class Config
{
    /**
     * Generates the config template as an object.
     *
     * @return \Generator
     */
    public function getFiles()
    {
        $origin = $this->getOrigin();

        yield ($origin instanceof \SplFileInfo) ? $origin : new \SplFileInfo($origin);
    }

    /**
     * Gets the origin configuration file.
     *
     * @return \SplFileInfo|string A file or directory path, or a \SplFileInfo object
     */
    public function getOrigin()
    {
        return realpath(__DIR__.'/../Resources/'.$this->getPath());
    }

    /**
     * Returns the template to render (or its path).
     *
     * @return \SplFileInfo|string
     */
    public function getTemplate()
    {
        return $this->getOrigin();
    }

    /**
     * Returns the path name of the configuration file or directory.
     *
     * Note: This needs to be concatened to a given working directory.
     *
     * @return string
     */
    abstract public function getPath();
}
