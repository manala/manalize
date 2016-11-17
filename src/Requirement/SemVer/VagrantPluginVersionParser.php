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
     * Regexp extracting a version of a vagrant plugin from a command output.
     *
     * @example 'ansible 1.9.4' gives '1.9.4'
     */
    const VERSION_PATTERN = '/%s\s\(([0-9]+\.[0-9]+\.[0-9]+)\)/';

    /**
     * {@inheritdoc}
     */
    public function getVersion(string $name, string $consoleOutput): string
    {
        $pattern = sprintf(self::VERSION_PATTERN, $name);
        preg_match($pattern, $consoleOutput, $matches);

        return $matches[1] ?? 0;
    }
}
