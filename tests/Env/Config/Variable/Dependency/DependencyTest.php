<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env\Config\Variable\Dependency;

use Manala\Env\Config\Variable\Dependency\Dependency;

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReplaces()
    {
        $var = new Dependency('brainfuck', true);

        $this->assertSame(['brainfuck' => true], $var->getReplaces());
    }

    public function testValidateDoesNothing()
    {
        $this->assertNull(Dependency::validate('dummyval'));
    }
}
