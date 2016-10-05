<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env\Config;

use Manala\Env\Config\Variable\Variable;
use Manala\Env\EnvEnum;

/**
 * Represents a config part of a Manala environment.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
abstract class Config
{
    /**
     * @var EnvEnum
     */
    protected $envType;

    /**
     * @var Variable[]
     */
    protected $vars;

    public function __construct(EnvEnum $envType, Variable ...$vars)
    {
        $this->envType = $envType;
        $this->vars = $vars;
    }

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
        return new \SplFileInfo(MANALA_DIR.'/src/Resources/'.$this->envType.'/'.$this->getPath());
    }

    /**
     * Returns the template to render (or its path).
     *
     * @return \SplFileInfo|null
     */
    abstract public function getTemplate();

    /**
     * Returns the path name of the configuration file or directory.
     *
     * Note: This needs to be concatenated to a given working directory.
     *
     * @return string
     */
    abstract public function getPath();

    /**
     * Returns the variables to be used for rendering the template.
     *
     * @return Variable[]
     */
    public function getVars()
    {
        return $this->vars;
    }
}
