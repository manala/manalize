<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Process;

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\Env;
use Symfony\Component\Filesystem\Filesystem;

class DumperTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        (new Filesystem())->mkdir(sys_get_temp_dir().'/Manala/DumperTest');
    }

    public function testDump()
    {
        $baseOrigin = sys_get_temp_dir().'/Manala/DumperTest';

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
        $env
            ->export()
            ->willReturn([
                'env' => 'dummy',
                'vars' => [
                    '{{ app }}' => 'dummy',
                    '{{ version }}' => '1.2.0',
                ],
            ]);

        $cwd = $baseOrigin.'/target';
        @mkdir($cwd);

        Dumper::dump($env->reveal(), $cwd)->current();

        $this->assertFileExists($cwd.'/dummy/dummyconf');
        $this->assertStringEqualsFile($cwd.'/ansible/.manalize.yml', <<<'YAML'
envs:
    dummy:
        vars:
            '{{ app }}': dummy
            '{{ version }}': 1.2.0

YAML
        );
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
