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
use Manala\Env\Config\Variable\Variable;
use Manala\Env\Env;
use Prophecy\Prophecy\ObjectProphecy;

class EnvTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $config = $this->prophesize(Config::class);
        $env = new Env('foo', $config->reveal());

        $this->assertSame([$config->reveal()], $env->getConfigs());
    }

    public function testExport()
    {
        /** @var Variable|ObjectProphecy $var */
        $var = $this->prophesize(Variable::class);
        $var->getReplaces()->willReturn([
           '{{ app }}' => 'dummy',
           '{{ version }}' => '1.2.0',
        ]);

        /** @var Config|ObjectProphecy $config */
        $config = $this->prophesize(Config::class);
        $config->getVars()->willReturn([$var]);
        $env = new Env('foo', $config->reveal());

        $this->assertEquals([
            'env' => 'foo',
            'vars' => [
                '{{ app }}' => 'dummy',
                '{{ version }}' => '1.2.0',
            ],
        ], $env->export());
    }
}
