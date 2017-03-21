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

use Manala\Manalize\Env\Config\Variable\Package;

class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReplaces()
    {
        $var = new Package('brainfuck', true, '2.6');

        $this->assertSame(['brainfuck_version' => '2.6', 'brainfuck_enabled' => 'true'], $var->getReplaces());
    }

    public function testValidate()
    {
        $this->assertSame('2.6', Package::validate('2.6', '2.6|~3.0'));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Version "2.6" doesn't match constraint "~3.0"
     */
    public function testValidateFailsOnIncompatibleVersion()
    {
        Package::validate('2.6', '~3.0');
    }
}
