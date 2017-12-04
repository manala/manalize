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
        $cwd = manala_get_tmp_dir('tests_setup_');
        mkdir($cwd = $cwd.'/manalized-app');

        self::$cwd = $cwd;
    }

    public function tearDown()
    {
        (new Filesystem())->remove(self::$cwd);
    }

    /**
     * @dataProvider provideEnvs()
     */
    public function testExecute(array $inputs, $expectedBoxName, $expectedBoxVersion, $expectedDeps, $expectedMetadataFilename)
    {
        self::createSymfonyStandardProject(self::$cwd);
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs($inputs)
            ->execute(['cwd' => static::$cwd, '--env' => EnvName::SYMFONY]);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully configured', $tester->getDisplay());

        $this->assertFileExists(self::$cwd.'/Vagrantfile');
        $this->assertFileExists(self::$cwd.'/Makefile');
        $this->assertFileExists(self::$cwd.'/ansible/group_vars/app.yml');
        $this->assertFileExists(self::$cwd.'/ansible/app.yml');
        $this->assertFileExists(self::$cwd.'/ansible/ansible.yml');
        $this->assertFileExists(self::$cwd.'/ansible/.gitignore');
        $this->assertFileExists(self::$cwd.'/.gitignore');

        $fixturesDir = self::FIXTURES_DIR.'/Command/SetupTest';
        $vagrantFile = file_get_contents(self::$cwd.'/Vagrantfile');

        $this->assertContains(":name        => '$expectedBoxName'", $vagrantFile);
        $this->assertContains(":box_version => '$expectedBoxVersion'", $vagrantFile);

        if (UPDATE_FIXTURES) {
            file_put_contents("$fixturesDir/$expectedDeps", file_get_contents(self::$cwd.'/ansible/group_vars/app.yml'));
            file_put_contents("$fixturesDir/$expectedMetadataFilename", file_get_contents(self::$cwd.'/ansible/.manalize.yml'));
        }

        $this->assertFileEquals("$fixturesDir/$expectedDeps", self::$cwd.'/ansible/group_vars/app.yml');
        $this->assertFileEquals("$fixturesDir/$expectedMetadataFilename", self::$cwd.'/ansible/.manalize.yml');
    }

    public function testExecuteEnvNameChoice()
    {
        $inputs = ['0', "\n", "\n", "\n"];
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs($inputs)
            ->execute(['cwd' => static::$cwd])
        ;

        $consoleDisplay = $tester->getDisplay();
        $this->assertFileExists(self::$cwd.'/Vagrantfile');
        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Select your environment', $consoleDisplay);
        $this->assertContains('Environment successfully configured', $consoleDisplay);
    }

    public function testExecuteEnvNameWithIncorrectChoice()
    {
        $countEnvs = count(EnvName::values());
        $inputs = [(string) $countEnvs, '0', "\n", "\n", "\n", "\n"];
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs($inputs)
            ->execute(['cwd' => static::$cwd])
        ;

        $consoleDisplay = $tester->getDisplay();
        $this->assertFileExists(self::$cwd.'/Vagrantfile');
        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Select your environment', $consoleDisplay);
        $this->assertContains(sprintf('[ERROR] Value "%u" is invalid ', $countEnvs), $consoleDisplay, 'Out of bounds choice should display an error');
        $this->assertContains('Environment successfully configured', $consoleDisplay);
    }

    public function provideEnvs()
    {
        return [
            [
                ["\n", "\n"],
                'manalized-app',
                '~> 3.0.0',
                'app_1.yml',
                'metadata_1.yml',
            ],
            [
              ['foo-bar.manala', 'yes', '5.6', "\n", "\n", "\n", "\n", "\n", "\n"],
                'foo-bar.manala',
                '~> 2.0.0',
                'app_2.yml',
                'metadata_2.yml',
            ],
            [
                ['foo-bar.manala', "\n"],
                'foo-bar.manala',
                '~> 3.0.0',
                'app_3.yml',
                'metadata_3.yml',
            ],
        ];
    }

    public function testExecuteNoUpdate()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(["\n", "\n"])
            ->execute(['cwd' => static::$cwd, '--no-update' => true, '--env' => 'symfony']);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully configured', $tester->getDisplay());

        $this->assertFileNotExists(self::$cwd.'/Vagrantfile');
        $this->assertFileNotExists(self::$cwd.'/Makefile');
        $this->assertFileNotExists(self::$cwd.'/ansible/group_vars/app.yml');
        $this->assertFileNotExists(self::$cwd.'/ansible/app.yml');
        $this->assertFileNotExists(self::$cwd.'/ansible/ansible.yml');

        if (UPDATE_FIXTURES) {
            file_put_contents(__DIR__.'/../fixtures/Command/SetupTest/execute_no_update.yml', file_get_contents(self::$cwd.'/ansible/.manalize.yml'));
        }

        $this->assertFileEquals(self::$cwd.'/ansible/.manalize.yml', __DIR__.'/../fixtures/Command/SetupTest/execute_no_update.yml');
    }

    public function testExecuteHandleConflicts()
    {
        touch(self::$cwd.'/Vagrantfile'); // add a conflicting file

        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(["\n", "\n", '0']) // patch strategy
            ->execute(['cwd' => self::$cwd, '--env' => 'symfony']);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertContains('Environment successfully configured', $tester->getDisplay());

        $this->assertFileExists(self::$cwd.'/Makefile');
        $this->assertFileExists(self::$cwd.'/ansible/group_vars/app.yml');
        $this->assertFileExists(self::$cwd.'/ansible/app.yml');
        $this->assertFileExists(self::$cwd.'/ansible/ansible.yml');

        $this->assertSame('', file_get_contents(self::$cwd.'/Vagrantfile'));
        $this->assertFileExists(self::$cwd.'/manalize.patch');

        $expected = '';
        (new Diff(EnvName::SYMFONY(), self::$cwd, false))->handle(function ($diff) use (&$expected) {
            $expected .= $diff;
        });

        $this->assertSame($expected, file_get_contents(self::$cwd.'/manalize.patch'));
    }
}
