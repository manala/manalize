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

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testGetConfig()
    {
        $config = $this->prophesize(Config::class);
        $env = new Env('foo', $config->reveal());

        $this->assertSame([$config->reveal()], $env->getConfigs());
    }
}
