<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\EnvGuesser;

use Manala\Manalize\Env\TemplateName;

/**
 * Interface for classes that are able to guess the better env from a project configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
interface EnvGuesserInterface
{
    /**
     * Guesses the TemplateName to use from a given project config.
     *
     * @param \SplFileInfo $config a config file or directory
     *
     * @return TemplateName|void
     */
    public function guess(\SplFileInfo $config);

    /**
     * @return bool whether the implementer supports guessing from the given config
     */
    public function supports(\SplFileInfo $config): bool;
}
