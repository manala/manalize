<?php

namespace RCH\Manalize\Config;

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
        yield new \SplFileInfo($this->getOrigin());
    }

    /**
     * Gets the origin configuration path.
     *
     * @return \SplFileInfo|string A file or directory path, or a \SplFileInfo object
     */
    public function getOrigin()
    {
        return realpath(__DIR__.'/../Resources/'.$this->getPath());
    }

    /**
     * Gets the target configuration path.
     *
     * @return \SplFileInfo|string A file or directory path, or a \SplFileInfo object
     */
    public function getTarget()
    {
        return getcwd().DIRECTORY_SEPARATOR.$this->getPath();
    }

    /**
     * Returns the path of the template to be rendered.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->getOrigin();
    }

    /**
     * Returns the raw resource path (without prepending any base directory).
     *
     * @return string
     */
    abstract protected function getPath();
}
