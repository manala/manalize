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

use Manala\Manalize\Env\TemplateName;

/**
 * Guesses the env based on a composer.json file.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class ComposerEnvGuesser implements EnvGuesserInterface
{
    private static $envMap = [
        'symfony/symfony' => TemplateName::ELAO_SYMFONY,
    ];

    /**
     * {@inheritdoc}
     */
    public function guess(\SplFileInfo $config)
    {
        if ($config->isDir()) {
            $config = $this->findComposerDotJson($config);
        }

        if (!$config) {
            return;
        }

        $rawConfig = json_decode(file_get_contents($config), true);

        if (!isset($rawConfig['require'])) {
            return;
        }

        foreach ($rawConfig['require'] as $package => $version) {
            if (isset(self::$envMap[$package])) {
                return TemplateName::get(self::$envMap[$package]);
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

        return is_file($expectedPath) ? new \SplFileInfo("$directory/composer.json") : false;
    }
}
