<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Requirement\SemVer;

use Manala\Manalize\Requirement\SemVer\VagrantPluginVersionParser;

class VagrantPluginVersionParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $name
     * @param string $consoleOutput
     * @param string $expectedVersion
     *
     * @dataProvider provideData
     */
    public function testBinaryVersionParser($name, $consoleOutput, $expectedVersion)
    {
        $parser = new VagrantPluginVersionParser();

        $this->assertEquals($parser->getVersion($name, $consoleOutput), $expectedVersion);
    }

    public function provideData()
    {
        return [
            [
                'landrush',
                'landrush (0.18.0)
                 vagrant-share (1.1.5, system)',
                '0.18.0',
            ],
            [
                'vagrant-plugin',
                'vagrant-share (1.1.5, system)
                 vagrant-plugin (1.11.15)',
                '1.11.15',
            ],
            [
                'anotherPlugin',
                'anotherPlugin (1.9.14)',
                '1.9.14',
            ],
        ];
    }
}
