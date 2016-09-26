<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Handler\Task;

use Symfony\Component\Process\Process;

/**
 * Vagrant task.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class VagrantTask extends Process
{
    /**
     * @param string         $cmd     The vagrant command to execute
     * @param string         $cwd     The working directory
     * @param int|float|null $timeout The process timeout
     */
    public function __construct($cmd, $cwd, $timeout = 60)
    {
        parent::__construct(sprintf('vagrant %s', $cmd), $cwd);

        $this->setTimeout($timeout);
    }
}
