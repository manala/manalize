<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Requirement\SemVer;

use Manala\Requirement\SemVer\BinaryVersionParser;

class BinaryVersionParserTest extends \PHPUnit_Framework_TestCase
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
        $parser = new BinaryVersionParser();

        $this->assertEquals($parser->getVersion($name, $consoleOutput), $expectedVersion);
    }

    public function provideData()
    {
        return [
            [
                'vagrant',
                'Vagrant 1.8.4',
                '1.8.4',
            ],
            [
                'php',
                'PHP 7.0.8 (cli) (built: Jun 23 2016 16:32:40) ( NTS )
                 Copyright (c) 1997-2016 The PHP Group
                 Zend Engine v3.0.0, Copyright (c) 1998-2016 Zend Technologies',
                '7.0.8',
            ],
            [
                'ansible',
                'ansible 1.9.4
                configured module search path = None',
                '1.9.4',
            ],
        ];
    }
}
