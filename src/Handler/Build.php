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

use Manala\Exception\HandlingFailureException;
use Manala\Handler\Task\VagrantTask;
use Symfony\Component\Process\Process;

/**
 * Build Handler, firstly intended to be consumed by the Build command.
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
    public function __construct($cwd)
    {
        $vagrantTasks = [
            'up --no-provision',
            'provision',
            'ssh -- "cd /srv/app && make install"',
        ];

        $this->createSubProcesses($vagrantTasks, $cwd);
    }

    /**
     * Handles the Build through running sub processes.
     *
     * @param callable|null $callback
     *
     * @return int The last process exit code
     */
    public function handle(callable $callback = null)
    {
        foreach ($this->subProcesses as $process) {
            $process->run($callback);
            $this->lastExitCode = $process->getExitCode();

            if (!$process->isSuccessful()) {
                $this->errorOutput = $process->getErrorOutput();

                throw new HandlingFailureException(sprintf(
                    'An error occurred while running process "%s". Use "%s::getErrorOutput()" for getting the error output.',
                    $process->getCommandLine(),
                    __CLASS__
                ));
            }
        }

        return $this->lastExitCode;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->lastExitCode;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return 0 === $this->lastExitCode;
    }

    /**
     * @return string
     */
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
