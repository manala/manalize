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
use Manala\Manalize\Env\EnvName;
use Manala\Manalize\Handler\Diff;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class SetupTest extends TestCase
{
    private static $cwd;

    public function setUp()
    {
        $cwd = manala_get_tmp_dir('tests_setup_').'/manalized-app';
        (new Filesystem())->mkdir($cwd);

        self::createSymfonyStandardProject($cwd);

        self::$cwd = $cwd;
    }

    public function tearDown()
    {
        (new Filesystem())->remove(self::$cwd);
    }

    /**
     * @dataProvider provideEnvs()
     */
    public function testExecute(array $inputs, $expectedDeps, $expectedManala)
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs($inputs)
            ->execute(['cwd' => static::$cwd, '--env' => 'elao-symfony']);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $fixturesDir = self::FIXTURES_DIR.'/Command/SetupTest';

        if (UPDATE_FIXTURES) {
            file_put_contents("$fixturesDir/$expectedDeps", file_get_contents(self::$cwd.'/ansible/group_vars/app.yaml'));
            file_put_contents("$fixturesDir/$expectedManala", file_get_contents(self::$cwd.'/manala.yaml'));
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully configured', $tester->getDisplay());

        $this->assertFileExists(self::$cwd.'/manala.yaml');
        $this->assertFileExists(self::$cwd.'/manala/Vagrantfile');
        $this->assertFileExists(self::$cwd.'/Makefile');
        $this->assertFileExists(self::$cwd.'/ansible/group_vars/app.yaml');
        $this->assertFileExists(self::$cwd.'/ansible/app.yaml');
        $this->assertFileExists(self::$cwd.'/ansible/ansible.yml');

        $vagrantFile = file_get_contents(self::$cwd.'/manala/Vagrantfile');

        $this->assertFileEquals("$fixturesDir/$expectedDeps", self::$cwd.'/ansible/group_vars/app.yaml');
        $this->assertFileEquals("$fixturesDir/$expectedManala", self::$cwd.'/manala.yaml');
    }

    public function provideEnvs()
    {
        return [
            [
                ["\n", "\n"],
                'app_1.yaml',
                'manala_1.yaml',
            ],
            [
              ['foo-bar.manala', 'yes', '5.6', "\n", "\n", "\n", "\n", "\n", "\n"],
                'app_2.yaml',
                'manala_2.yaml',
            ],
            [
                ['foo-bar.manala', "\n"],
                'app_3.yaml',
                'manala_3.yaml',
            ],
        ];
    }

    public function testExecuteNoUpdate()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(["\n", "\n"])
            ->execute(['cwd' => static::$cwd, '--no-update' => true, '--env' => 'elao-symfony']);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully configured', $tester->getDisplay());

        $this->assertFileNotExists(self::$cwd.'/manala/Vagrantfile');
        $this->assertFileNotExists(self::$cwd.'/Makefile');
        $this->assertFileNotExists(self::$cwd.'/ansible/group_vars/app.yaml');
        $this->assertFileNotExists(self::$cwd.'/ansible/app.yaml');
        $this->assertFileNotExists(self::$cwd.'/ansible/ansible.yml');

        if (UPDATE_FIXTURES) {
            file_put_contents(__DIR__.'/../fixtures/Command/SetupTest/execute_no_update.yaml', file_get_contents(self::$cwd.'/manala.yaml'));
        }

        $this->assertFileEquals(self::$cwd.'/manala.yaml', __DIR__.'/../fixtures/Command/SetupTest/execute_no_update.yaml');
    }

    public function testExecuteHandleConflicts()
    {
        @mkdir(self::$cwd.'/manala');
        @touch(self::$cwd.'/manala/Vagrantfile'); // add a conflicting file

        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(["\n", "\n", '0']) // patch strategy
            ->execute(['cwd' => self::$cwd, '--env' => 'elao-symfony']);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully configured', $tester->getDisplay());

        $this->assertFileExists(self::$cwd.'/manala.yaml');
        $this->assertFileExists(self::$cwd.'/Makefile');
        $this->assertFileExists(self::$cwd.'/ansible/group_vars/app.yaml');
        $this->assertFileExists(self::$cwd.'/ansible/app.yaml');
        $this->assertFileExists(self::$cwd.'/ansible/ansible.yml');

        $this->assertSame('', file_get_contents(self::$cwd.'/manala/Vagrantfile'));
        $this->assertFileExists(self::$cwd.'/manalize.patch');

        $expected = '';
        (new Diff(self::$cwd, EnvName::ELAO_SYMFONY(), false))->handle(function ($diff) use (&$expected) {
            $expected .= $diff;
        });

        $this->assertSame($expected, file_get_contents(self::$cwd.'/manalize.patch'));
    }
}
