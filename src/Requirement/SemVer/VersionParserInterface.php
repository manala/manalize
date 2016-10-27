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

interface VersionParserInterface
{
    /**
     * Get the required executable's version from its command output.
     *
     * @param string $name
     * @param string $consoleOutput
     *
     * @return string
     */
    public function getVersion(string $name, string $consoleOutput): string;
}
