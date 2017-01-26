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

use Manala\Manalize\Env\EnvName;

/**
 * Guesses the env based on a composer.json file.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class ComposerEnvGuesser implements EnvGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guess(\SplFileInfo $config)
    {
        if ($config->isDir()) {
            $config = $this->findComposerDotJson($config);
        }

        $rawConfig = json_decode(file_get_contents($config), true);

        if (!isset($rawConfig['require'])) {
            return;
        }

        foreach (array_keys($rawConfig['require']) as $package) {
            if (EnvName::accepts($name = $this->stripVendorName($package))) {
                return EnvName::get($name);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $config): bool
    {
        if ($config->isDir()) {
            return (bool) $this->findComposerDotJson($config);
        }

        return 'composer.json' === (string) $config;
    }

    private static function findComposerDotJson(\SplFileInfo $directory)
    {
        $expectedPath = "$directory/composer.json";

        return !is_file($expectedPath) ?: new \SplFileInfo("$directory/composer.json");
    }

    private static function stripVendorName(string $package)
    {
        return substr($package, strpos($package, '/') + 1);
    }
}
