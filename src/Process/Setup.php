<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Process;

use Manala\Manalize\Config\Config;
use Manala\Manalize\Config\Dumper;
use Manala\Manalize\Config\Vars;
use Manala\Manalize\Env\Env;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Manala setup process.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Setup extends Process
{
    /**
     * @param string $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct('make setup', $cwd);

        $this->setTimeout(null);
    }

    /**
     * Prepare configuration dumps for the given process.
     *
     * @param Env  $env
     * @param Vars $vars
     *
     * @return \Generator The config dumps
     */
    public function prepare(Env $env, Vars $vars)
    {
        $fs = new Filesystem();
        $cwd = parent::getWorkingDirectory();
        foreach ($env->getConfigs() as $config) {
            $baseTarget = $cwd.DIRECTORY_SEPARATOR.$config->getPath();
            $template = $config->getTemplate();
            foreach ($config->getFiles() as $file) {
                $target = str_replace($config->getOrigin(), $baseTarget, $file->getPathName());
                $dump = ((string) $template === $file->getRealPath()) ? Dumper::dump($config, $vars) : file_get_contents($file);
                $fs->dumpFile($target, $dump);

                yield $target => $dump;
            }
        }
    }

    /**
     * Starts and returns the running process.
     *
     * @return \IteratorAggregate
     */
    public function run($callback = null)
    {
        parent::start();

        return $this;
    }
}
