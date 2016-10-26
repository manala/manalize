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
    public function testExecute(array $inputs, $expectedBox, $expectedDeps, $expectedMetadataFilename)
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

        $this->assertContains($expectedBox, file_get_contents(self::$cwd.'/Vagrantfile'));
        $this->assertContains($expectedDeps, file_get_contents(self::$cwd.'/ansible/group_vars/app.yml'));
        $this->assertFileEquals(self::$cwd.'/ansible/.manalize', __DIR__.'/../fixtures/Command/SetupTest/'.$expectedMetadataFilename);
    }

    public function provideEnvs()
    {
        return [
            // name: project dir name, expected dependencies: default,
            [
                ["\n", "\n", "\n", "\n", "\n", "\n"],
                <<<'RUBY'
  :name        => 'manalized-app',
  :box         => 'manala/app-dev-debian',
  :box_version => '~> 3.0.0'
RUBY
,
                <<<'YAML'
  php:                   true
  php_version:           7.0
  nodejs:                false
  nodejs_version:        6
  mysql:                 true
  mysql_version:         5.6
  mongodb:               false
  mongodb_version:       3.2
  postgresql:            false
  postgresql_version:    9.5
  elasticsearch:         false
  elasticsearch_version: 1.7
  redis:                 false
  influxdb:              false
YAML
                ,
                'metadata_1.txt',
            ],
            // name: "foo-bar.manala", expected dependencies: php 5.6
            [
                ['foo-bar.manala', '5.6', "\n", "\n", "\n", "\n", "\n"],
                <<<'RUBY'
  :name        => 'foo-bar.manala',
  :box         => 'manala/app-dev-debian',
  :box_version => '~> 2.0.0'
RUBY
                , <<<'YAML'
  php:                   true
  php_version:           5.6
  nodejs:                false
  nodejs_version:        6
  mysql:                 true
  mysql_version:         5.6
  mongodb:               false
  mongodb_version:       3.2
  postgresql:            false
  postgresql_version:    9.5
  elasticsearch:         false
  elasticsearch_version: 1.7
  redis:                 false
  influxdb:              false
YAML
                ,
                'metadata_2.txt',
            ],
            // name: "foo-bar.manala", expected dependencies: default
            [
                ['foo-bar.manala', "\n", "\n", "\n", "\n", "\n"],
                <<<'RUBY'
  :name        => 'foo-bar.manala',
  :box         => 'manala/app-dev-debian',
  :box_version => '~> 3.0.0'
RUBY
                , <<<'YAML'
  php:                   true
  php_version:           7.0
  nodejs:                false
  nodejs_version:        6
  mysql:                 true
  mysql_version:         5.6
  mongodb:               false
  mongodb_version:       3.2
  postgresql:            false
  postgresql_version:    9.5
  elasticsearch:         false
  elasticsearch_version: 1.7
  redis:                 false
  influxdb:              false
YAML
                ,
                'metadata_3.txt',
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

        $this->assertFileEquals(self::$cwd.'/ansible/.manalize', __DIR__.'/../fixtures/Command/SetupTest/execute_no_update.txt');
    }
}
