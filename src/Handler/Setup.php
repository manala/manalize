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
use Manala\Manalize\Env\Config\Variable\Dependency\Dependency;
use Manala\Manalize\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Manalize\Env\Config\Variable\Tld;
use Manala\Manalize\Env\Defaults\Defaults;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Env\EnvName;
use Manala\Manalize\Exception\HandlingFailureException;

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
    private $tld;

    public function __construct(
        string $cwd,
        AppName $appName,
        EnvName $envName,
        Tld $tld,
        \Traversable $dependencies,
        array $options = []
    ) {
        $this->cwd = $cwd;
        $this->appName = $appName;
        $this->envName = $envName;
        $this->tld = $tld;
        $this->dependencies = $dependencies;
        $this->options = $this->normalizeOptions($options);
    }

    public function handle(callable $notifier, callable $existingFileCallback = null)
    {
        $env = EnvFactory::createEnv($this->envName, $this->appName, $this->tld, $this->dependencies);
        $dumper = new Dumper($this->cwd);

        try {
            foreach ($dumper->dump($env, $this->getDumperFlags(), $existingFileCallback) as $target) {
                $notifier(str_replace($this->cwd.'/', '', $target));
            }
        } catch (\RuntimeException $e) {
            throw new HandlingFailureException($e->getMessage(), 0, $e);
        }
    }

    public static function createDefaultDependencySet(Defaults $defaults)
    {
        foreach ($defaults->get('packages') as $name => $package) {
            $defaultVersion = $package['default'] ?? null;

            if (null === $defaultVersion) {
                yield new Dependency($name, $package['enabled']);

                continue;
            }

            yield new VersionBounded($name, $package['enabled'], $defaultVersion);
        }
    }

    public static function getChoicesForAlreadyExistingFile()
    {
        return [
            Dumper::DO_PATCH => 'Give me a patch that I can apply later',
            Dumper::DO_REPLACE => 'Replace it',
            Dumper::DO_NOTHING => 'Do nothing',
        ];
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
