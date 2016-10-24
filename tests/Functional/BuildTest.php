<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Functional;

use Symfony\Component\Process\Process;

/**
 * @group infra
 */
class BuildTest extends TestCase
{
    private static $cwd;

    public static function setUpBeforeClass()
    {
        self::$cwd = manala_get_tmp_dir('tests_build_');
        self::createManalizedProject(self::$cwd, 'build-test');
    }

    public function testExecute()
    {
        $process = new Process('make setup', static::$cwd);
        $process
          ->setTimeout(null)
          ->run();

        if (0 !== $process->getExitCode()) {
            echo "stdout:\n".$process->getOutput()."\nstderr:\n".$process->getErrorOutput();
        }

        $this->assertSame(0, $process->getExitCode());

        foreach (['/vendor', '/.vagrant'] as $dir) {
            $this->assertTrue(is_dir(self::$cwd.$dir));
        }
    }

    public static function tearDownAfterClass()
    {
        (new Process(sprintf('cd %s && vagrant destroy --force && cd %s', self::$cwd, getcwd())))
            ->setTimeout(null)
            ->run();

        parent::tearDownAfterClass();
    }
}
