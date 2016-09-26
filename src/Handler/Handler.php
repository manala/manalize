<?php

namespace Manala\Handler;

/**
 * To be implemented by classes containing logic mostly intended to be used by commands, allowing to
 * reuse its login from everywhere else.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
interface Handler
{
    /**
     * Handles the Build through running sub processes.
     *
     * @param callable|null $callback A function to be executed during the handling,
     *                                e.g. for writing to the output of the caller command.
     *
     * @return mixed
     */
    public function handle(callable $callback = null)
}
