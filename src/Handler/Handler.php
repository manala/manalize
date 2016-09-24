<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Handler;

/**
 * To be implemented by classes containing logic mostly intended to be used by commands, allowing to
 * reuse its logic from everywhere else.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
interface Handler
{
    /**
     * Handles the Build through running sub processes.
     *
     * @param callable|null $callback A function to be executed during the handling,
     *                                e.g. for writing to the output of the caller command
     *
     * @return mixed
     */
    public function handle(callable $callback = null);
}
