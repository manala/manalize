<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env\Config\Variable;

use Manala\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Env\Config\Variable\VagrantBoxVersion;
use Manala\Env\Config\Variable\VagrantBoxVersionResolver;

class VagrantBoxVersionResolverTest extends \PHPUnit_Framework_TestCase
{
    public function getBoxVersionsForPhpVersions()
    {
        return [
            ['~> 3.0.0', '7.0'],
            ['~> 2.0.0', '5.6'],
            ['~> 2.0.0', '5.5'],
            ['~> 2.0.0', '5.4'],
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
