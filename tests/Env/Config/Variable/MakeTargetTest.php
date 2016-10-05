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

use Manala\Env\Config\Variable\MakeTarget;

class MakeTargetTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReplaces()
    {
        $var = new MakeTarget('dummy', ['cmd1', 'cmd2', 'cmd3']);

        $this->assertSame(['{{ dummy_tasks }}' => "cmd1\n\tcmd2\n\tcmd3"], $var->getReplaces());
    }

    public function testValidateDoesNothing()
    {
        $this->assertNull(MakeTarget::validate('dummyval'));
    }
}
