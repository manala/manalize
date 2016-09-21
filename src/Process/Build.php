<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Process;

use Manala\Process\Task\VagrantTask;
use Symfony\Component\Process\Process;

/**
 * Manala build process.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Build
{
    /**
     * @var Process[]
     */
    private $subProcesses = [];

    /**
     * @var int
     */
    private $lastExitCode;

    /**
     * @var string
     */
    private $errorOutput;

    /**
     * @param string $cwd
     */
    public function __construct($cwd, $tasks = [])
    {
        $vagrantTasks = [
            'up --no-provision',
            'provision',
            'ssh -- "cd /srv/app && make install"',
        ];

        $this->createSubProcesses(array_merge($vagrantTasks, $tasks), $cwd);
    }

    /**
     * Starts and returns the running process.
     *
     * @return \IteratorAggregate
     */
    public function run($callback = null)
    {
        foreach ($this->subProcesses as $process) {
            $process->run($callback);
            $this->lastExitCode = $process->getExitCode();

            if (!$process->isSuccessful()) {
                $this->errorOutput = $process->getErrorOutput();

                break;
            }
        }
    }

    public function getExitCode()
    {
        return $this->lastExitCode;
    }

    public function isSuccessful()
    {
        return 0 === $this->lastExitCode;
    }

    public function getErrorOutput()
    {
        return $this->errorOutput;
    }

    private function createSubProcesses(array $vagrantTasks, $cwd)
    {
        foreach ($vagrantTasks as $task) {
            $this->subProcesses[] = new VagrantTask($task, $cwd, null);
        }
    }
}
