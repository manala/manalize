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

class SetupTest extends TestCase
{
    private static $cwd;

    public function setUp()
    {
        $cwd = manala_get_tmp_dir('tests_setup_');
        mkdir($cwd = $cwd.'/manalized-app');

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
    public function testExecute(array $inputs, $expectedBoxName, $expectedBoxVersion, $expectedDeps, $expectedMetadataFilename)
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs($inputs)
            ->execute(['cwd' => static::$cwd]);

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

        $fixturesDir = self::FIXTURES_DIR.'/Command/SetupTest';
        $vagrantFile = file_get_contents(self::$cwd.'/Vagrantfile');

        $this->assertContains(":name        => '$expectedBoxName'", $vagrantFile);
        $this->assertContains(":box_version => '$expectedBoxVersion'", $vagrantFile);
        $this->assertContains(file_get_contents("$fixturesDir/$expectedDeps"), file_get_contents(self::$cwd.'/ansible/group_vars/app.yml'));
        $this->assertFileEquals(self::$cwd.'/ansible/.manalize.yml', "$fixturesDir/$expectedMetadataFilename");
    }

    public function provideEnvs()
    {
        return [
            [
                ["\n", "\n"],
                'manalized-app',
                '~> 3.0.0',
                'dependencies_1.yml',
                'metadata_1.yml',
            ],
            [
              ['foo-bar.manala', 'yes', '5.6', "\n", "\n", "\n", "\n", "\n"],
                'foo-bar.manala',
                '~> 2.0.0',
                'dependencies_2.yml',
                'metadata_2.yml',
            ],
            [
                ['foo-bar.manala', "\n"],
                'foo-bar.manala',
                '~> 3.0.0',
                'dependencies_3.yml',
                'metadata_3.yml',
            ],
        ];
    }

    public function testExecuteNoUpdate()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(["\n", "\n", "\n", "\n", "\n", "\n"])
            ->execute(['cwd' => static::$cwd, '--no-update' => true]);

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
        $this->assertFileEquals(self::$cwd.'/ansible/.manalize.yml', __DIR__.'/../fixtures/Command/SetupTest/execute_no_update.yml');
    }
}
