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

class RendererTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        file_put_contents(MANALIZE_HOME.'/templates/template', 'foo {# placeholder #} baz');
        $var = $this->prophesize(Variable::class);
        $var->getReplaces()->willReturn(['placeholder' => 'bar']);
        $config = $this->prophesize(Config::class);
        $config->getVars()->willReturn([$var->reveal()]);
        $config->getTemplate()->willReturn(new \SplFileInfo(MANALIZE_HOME.'/templates/template'));

        $this->assertSame('foo bar baz', (new Renderer())->render($config->reveal()));
    }

    public static function tearDownAfterClass()
    {
        @unlink(MANALIZE_HOME.'/templates/template');
        @unlink(MANALIZE_HOME.'/templates/template.yml');

        parent::tearDownAfterClass();
    }
}
