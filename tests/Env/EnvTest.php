<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env;

use Manala\Env\Config\Config;
use Manala\Env\Env;

class EnvTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $config = $this->prophesize(Config::class);
        $env = new Env($config->reveal());

        $this->assertSame([$config->reveal()], $env->getConfigs());
    }
}
