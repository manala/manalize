<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Config;

use Manala\Env\Config\Config;
use Manala\Env\Config\Renderer;
use Manala\Env\Config\Variable\Variable;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    private static $tempdir;

    public static function setUpBeforeClass()
    {
        self::$tempdir = sys_get_temp_dir().'/ManalaRendererTest';
        @mkdir(self::$tempdir);
    }

    public function testRender()
    {
        file_put_contents(self::$tempdir.'/template', 'foo {{ placeholder }} baz');
        $var = $this->prophesize(Variable::class);
        $var->getReplaces()->willReturn(['{{ placeholder }}' => 'bar']);
        $config = $this->prophesize(Config::class);
        $config->getVars()->willReturn([$var->reveal()]);
        $config->getTemplate()->willReturn(new \SplFileInfo(self::$tempdir.'/template'));

        $this->assertSame('foo bar baz', Renderer::render($config->reveal()));
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$tempdir.'/template');
        @unlink(self::$tempdir.'/template.yml');
        @rmdir(self::$tempdir);
    }
}
