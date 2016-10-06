<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Requirement\SemVer;

class VagrantPluginVersionParser implements VersionParserInterface
{
    /**
     * Example: landrush (0.18.0).
     */
    const OUTPUT_PATTERN = '/%s\s\(([0-9]+\.[0-9]+\.[0-9]+)\)/';

    /**
     * {@inheritdoc}
     */
    public function getVersion($name, $consoleOutput)
    {
        $pattern = sprintf(self::OUTPUT_PATTERN, $name);
        preg_match($pattern, $consoleOutput, $matches);
        $version = isset($matches[1]) ? $matches[1] : 0;

        return $version;
    }
}
