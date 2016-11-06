<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config;

use Manala\Manalize\Env\Config\Variable\Variable;
use Manala\Manalize\Env\EnvEnum;

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
    protected $envName;

    /**
     * @var Variable[]
     */
    protected $vars;

    public static function create(EnvEnum $envName, array $vars)
    {
        return new static($envName, ...$vars);
    }

    public function __construct(EnvEnum $envName, Variable ...$vars)
    {
        $this->envName = $envName;
        $this->vars = $vars;
    }

    /**
     * Gets the files of this configuration.
     *
     * @return \Generator A collection of \SplFileInfo instances
     */
    public function getFiles(): \Generator
    {
        $origin = $this->getOrigin();

        yield ($origin instanceof \SplFileInfo) ? $origin : new \SplFileInfo($origin);
    }

    /**
     * Gets the configuration template(s).
     *
     * @return \SplFileInfo
     */
    public function getOrigin(): \SplFileInfo
    {
        return new \SplFileInfo(MANALIZE_DIR.'/src/Resources/'.$this->envName.'/'.$this->getPath());
    }

    /**
     * Returns the template to render.
     *
     * @return \SplFileInfo
     */
    public function getTemplate(): \SplFileInfo
    {
        return $this->getOrigin();
    }

    /**
     * Returns the path name of the configuration file or directory.
     *
     * Note: This needs to be concatenated to a given working directory.
     *
     * @return string
     */
    abstract public function getPath(): string;

    /**
     * Returns the variables to be used for rendering the template.
     *
     * @return Variable[]
     */
    public function getVars(): array
    {
        return $this->vars;
    }
}
