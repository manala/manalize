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
    private $envName;
    private $dependencies;
    private $options;

    public function __construct(
        string $cwd,
        AppName $appName,
        EnvEnum $envName,
        \Traversable $dependencies,
        array $options = []
    ) {
        $this->cwd = $cwd;
        $this->appName = $appName;
        $this->envName = $envName;
        $this->dependencies = $dependencies;
        $this->options = $this->normalizeOptions($options);
    }

    public function handle(callable $notifier)
    {
        $env = EnvFactory::createEnv($this->envName, $this->appName, $this->dependencies);

        foreach (Dumper::dump($env, $this->cwd, $this->getDumperFlags()) as $target) {
            $notifier(str_replace($this->cwd.'/', '', $target));
        }
    }

    private function normalizeOptions(array $options): array
    {
        if (!isset($options['dumper_flags'])) {
            $options['dumper_flags'] = Dumper::DUMP_ALL;
        }

        return $options;
    }

    private function getDumperFlags(): int
    {
        return $this->options['dumper_flags'];
    }
}
