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

use Manala\Manalize\Command\Setup;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @group infra
 */
class BuildTest extends \PHPUnit_Framework_TestCase
{
    private static $cwd;

    public static function setUpBeforeClass()
    {
        $cwd = sys_get_temp_dir().'/Manala/tests/build';
        $fs = new Filesystem();

        if ($fs->exists($cwd)) {
            $fs->remove($cwd);
        }

        $fs->mkdir($cwd);

        (new Process('composer create-project symfony/framework-standard-edition:3.1.* . --no-install --no-progress --no-interaction', $cwd))
            ->setTimeout(null)
            ->run();

        (new CommandTester(new Setup()))
            ->setInputs(['manala.dummy', "\n", "\n", "\n", "\n", "\n", "\n"])
            ->execute(['cwd' => $cwd]);

        self::$cwd = $cwd;
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

        (new Filesystem())->remove(self::$cwd);
    }
}
