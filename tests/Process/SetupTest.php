<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Process;

use Manala\Config\Config;
use Manala\Config\Vars;
use Manala\Env\Env;
use Manala\Process\Setup;
use Symfony\Component\Filesystem\Filesystem;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        (new Filesystem())->mkdir(sys_get_temp_dir().'/Manala/SetupProcessTest');
    }

    public function testPrepare()
    {
        $baseOrigin = sys_get_temp_dir().'/Manala/SetupProcessTest';

        @mkdir($baseOrigin.'/dummy');
        file_put_contents($baseOrigin.'/dummy/dummyconf', 'FooBar');

        $config = $this->prophesize(Config::class);
        $config
            ->getPath()
            ->willReturn('dummy');
        $config
            ->getOrigin()
            ->willReturn($baseOrigin.'/dummy');
        $config
            ->getFiles()
            ->willReturn($this->generateFile($baseOrigin.'/dummy/dummyconf'));
        $config
            ->getTemplate()
            ->willReturn(null);

        $env = $this->prophesize(Env::class);
        $env
            ->getConfigs()
            ->willReturn([$config->reveal()]);

        $cwd = $baseOrigin.'/target';
        @mkdir($cwd);

        $setup = new Setup($cwd);
        $setup->prepare($env->reveal(), new Vars('manala'))->current();

        $this->assertFileExists($cwd.'/dummy/dummyconf');
    }

    public static function tearDownAfterClass()
    {
        (new Filesystem())->remove(sys_get_temp_dir().'/Manala/SetupProcessTest');
    }

    private function generateFile($path)
    {
        yield new \SplFileInfo($path);
    }
}
