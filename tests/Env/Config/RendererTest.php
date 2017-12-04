<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Config;

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Config\Renderer;
use Manala\Manalize\Env\Config\Variable\Variable;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    private static $tempdir;

    public static function setUpBeforeClass()
    {
        self::$tempdir = sys_get_temp_dir().'/ManalaRendererTest';
        @mkdir(self::$tempdir);
    }

    public function testRender()
    {
        file_put_contents(self::$tempdir.'/template', 'foo {# placeholder #} baz');
        $var = $this->prophesize(Variable::class);
        $var->getReplaces()->willReturn(['placeholder' => 'bar']);
        $config = $this->prophesize(Config::class);
        $config->getVars()->willReturn([$var->reveal()]);
        $config->getTemplate()->willReturn(new \SplFileInfo(self::$tempdir.'/template'));

        $this->assertSame('foo bar baz', (new Renderer())->render($config->reveal()));
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$tempdir.'/template');
        @unlink(self::$tempdir.'/template.yml');
        @rmdir(self::$tempdir);
    }
}
