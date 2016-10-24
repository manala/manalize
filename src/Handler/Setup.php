<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Handler;

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvEnum;
use Manala\Manalize\Env\EnvFactory;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Setup
{
    private $cwd;
    private $appName;
    private $envType;
    private $dependencies;

    public function __construct($cwd, AppName $appName, EnvEnum $envType, \Iterator $dependencies)
    {
        $this->cwd = $cwd;
        $this->appName = $appName;
        $this->envType = $envType;
        $this->dependencies = $dependencies;
    }

    public function handle(callable $notifier)
    {
        $env = EnvFactory::createEnv($this->envType, $this->appName, $this->dependencies);

        foreach (Dumper::dump($env, $this->cwd) as $target) {
            $notifier(str_replace($this->cwd.'/', '', $target));
        }

        $notifier(str_replace($this->cwd.'/', '', Dumper::dumpMetadata($env, $this->cwd)));
    }
}
