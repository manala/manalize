<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env\Config\Variable;

use Manala\Manalize\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersionResolver;
use PHPUnit\Framework\TestCase;

class VagrantBoxVersionResolverTest extends TestCase
{
    public function getBoxVersionsForPhpVersions()
    {
        return [
            ['~> 4.0.5', '7.2'],
            ['~> 4.0.5', '7.1'],
            ['~> 4.0.5', '7.0'],
            ['~> 2.0.10', '5.6'],
            ['~> 2.0.10', '5.5'],
            ['~> 2.0.10', '5.4'],
        ];
    }

    /**
     * @dataProvider getBoxVersionsForPhpVersions
     */
    public function testResolve($expectedBoxVersion, $phpVersion)
    {
        $this->assertEquals(
            new VagrantBoxVersion($expectedBoxVersion),
            VagrantBoxVersionResolver::resolve($this->getDependencies($phpVersion))
        );
    }

    private function getDependencies($phpVersion)
    {
        yield new VersionBounded('php', true, $phpVersion);
    }
}
