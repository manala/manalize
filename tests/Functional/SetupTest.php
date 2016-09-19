<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Functional;

use Manala\Command\Setup;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @group infra
 */
class SetupTest extends \PHPUnit_Framework_TestCase
{
    private static $cwd;

    public static function setUpBeforeClass()
    {
        $cwd = sys_get_temp_dir().'/Manala';
        $fs = new Filesystem();

        if ($fs->exists($cwd)) {
            $fs->remove($cwd);
        }

        $fs->mkdir($cwd);

        (new Process('composer create-project symfony/framework-standard-edition:3.1.* . --no-install --no-progress --no-interaction', $cwd))
            ->setTimeout(null)
            ->run();

        self::$cwd = $cwd;
    }

    public function testExecute()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(['manala', 'dummy'])
            ->execute(['cwd' => static::$cwd]);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully created', $tester->getDisplay());

        $this->assertFileExists(self::$cwd.'/Vagrantfile');
        $this->assertFileExists(self::$cwd.'/Makefile');

        foreach (['/ansible', '/vendor', '/.vagrant'] as $dir) {
            $this->assertTrue(is_dir(self::$cwd.$dir));
        }

        $vagrantFile = file_get_contents(self::$cwd.'/Vagrantfile');
        $this->assertContains(":name        => 'dummy.manala'", $vagrantFile);
    }

    public static function tearDownAfterClass()
    {
        (new Process(sprintf('cd %s && vagrant destroy --force && cd %s', self::$cwd, getcwd())))->run();
        (new Filesystem())->remove(self::$cwd);
    }
}
