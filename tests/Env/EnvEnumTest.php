<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env;

use Manala\Manalize\Env\EnvEnum;

class EnvEnumTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $envType = EnvEnum::create(EnvEnum::SYMFONY);

        $this->assertInstanceOf(EnvEnum::class, $envType);
        $this->assertSame('symfony', (string) $envType);
    }

    /**
     * @expectedException        \Manala\Manalize\Exception\InvalidEnvException
     * @expectedExceptionMessage The env "dummy" doesn't exist. Possible values: ["symfony"]
     */
    public function testCreateUndefinedEnv()
    {
        EnvEnum::create('dummy');
    }
}
