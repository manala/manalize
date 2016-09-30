<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\SemVer;

class BinaryVersionParser implements VersionParserInterface
{
    /**
     * Example: ansible 1.9.4 [...].
     */
    const OUTPUT_PATTERN = '/^[a-zA-Z0-9]+\s([0-9]+\.[0-9]+\.[0-9]+)/';

    /**
     * {@inheritdoc}
     */
    public function getVersion($name, $consoleOutput)
    {
        preg_match(self::OUTPUT_PATTERN, $consoleOutput, $matches);
        $version = isset($matches[1]) ? $matches[1] : 0;

        return $version;
    }
}
