<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\SemVer;

class VagrantPluginVersionParser implements VersionParserInterface
{
    /**
     * Example: landrush (0.18.0).
     */
    const OUTPUT_PATTERN = '/%s\s\(([0-9]+\.[0-9]+\.[0-9]+)\)/';

    /**
     * {@inheritdoc}
     */
    public function getVersion(string $name, string $consoleOutput): string
    {
        $pattern = sprintf(self::OUTPUT_PATTERN, $name);
        preg_match($pattern, $consoleOutput, $matches);
        $version = isset($matches[1]) ? $matches[1] : 0;

        return $version;
    }
}
