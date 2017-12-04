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

use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use PHPUnit\Framework\TestCase;

class VagrantBoxVersionTest extends TestCase
{
    public function testGetReplaces()
    {
        $var = new VagrantBoxVersion('~> 3.0.0');

        $this->assertSame(['box_version' => '~> 3.0.0'], $var->getReplaces());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The "~> 1.0.0" version doesn't exist or is not supported.
     */
    public function testValidateFailsForUnsupportedVersion()
    {
        VagrantBoxVersion::validate('~> 1.0.0');
    }

    public function testValidate()
    {
        $this->assertSame('~> 2.0.0', VagrantBoxVersion::validate('~> 2.0.0'));
    }
}
