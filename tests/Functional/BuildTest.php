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

use Manala\Manalize\Env\EnvName;
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
        $envType = EnvName::ELAO_SYMFONY();

        self::createManalizedProject(
            self::$cwd,
            'build-test',
            $envType,
            self::enableDependency(self::getDefaultDependenciesForEnv($envType), 'mysql')
        );
    }

    public function testExecute()
    {
        $process = new Process('make setup', static::$cwd);
        $process
          ->setTimeout(null)
          ->run();

        if (0 !== $process->getExitCode()) {
            echo "stdout:\n".$process->getOutput()."\nstderr:\n".$process->getErrorOutput();
            $this->tearDown();
        }

        $this->assertSame(0, $process->getExitCode());

        foreach (['/vendor', '/.vagrant'] as $dir) {
            $this->assertTrue(is_dir(self::$cwd.$dir));
        }
    }

    protected function tearDown()
    {
        (new Process('vagrant destroy --force', self::$cwd))
            ->setTimeout(null)
            ->run();

        parent::tearDown();
    }
}
