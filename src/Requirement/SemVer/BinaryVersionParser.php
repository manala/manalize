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

class BinaryVersionParser implements VersionParserInterface
{
    /**
     * Regexp extracting a version from a command output.
     *
     * @example 'ansible 1.9.4' gives '1.9.4'
     */
    const VERSION_PATTERN = '/\d+\.\d+\.\d+/';

    /**
     * {@inheritdoc}
     */
    public function getVersion(string $name, string $consoleOutput): string
    {
        preg_match(self::VERSION_PATTERN, $consoleOutput, $matches);

        return $matches[0] ?? 0;
    }
}
