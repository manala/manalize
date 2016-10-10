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

class SetupTest extends \PHPUnit_Framework_TestCase
{
    private static $cwd;

    public static function setUpBeforeClass()
    {
        $cwd = manala_get_tmp_dir('tests_setup_');
        mkdir($cwd = $cwd.'/manalized-app');

        (new Process('composer create-project symfony/framework-standard-edition:3.1.* . --no-install --no-progress --no-interaction', $cwd))
            ->setTimeout(null)
            ->run();

        self::$cwd = $cwd;
    }

    public function testExecute()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(['foo-bar.manala', "\n", "\n", "\n", "\n", "\n"])
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

        $expectedBox = <<<'RUBY'
  :name        => 'foo-bar.manala',
  :box         => 'manala/app-dev-debian',
  :box_version => '~> 3.0.0'
RUBY;

        $this->assertContains($expectedBox, file_get_contents(self::$cwd.'/Vagrantfile'));

        $expectedDeps = <<<'YAML'
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
YAML;

        $this->assertContains($expectedDeps, file_get_contents(self::$cwd.'/ansible/group_vars/app.yml'));
        $this->assertStringEqualsFile(self::$cwd.'/ansible/.manalize.yml', <<<'YAML'
envs:
    symfony:
        vars:
            '{{ app }}': foo-bar.manala
            '{{ box_version }}': '~> 3.0.0'
            '{{ php_version }}': '7.0'
            '{{ php_enabled }}': 'true'
            '{{ mysql_version }}': '5.6'
            '{{ mysql_enabled }}': 'true'
            '{{ postgresql_version }}': '9.5'
            '{{ postgresql_enabled }}': 'false'
            '{{ mongodb_version }}': '3.2'
            '{{ mongodb_enabled }}': 'false'
            '{{ elasticsearch_version }}': '1.7'
            '{{ elasticsearch_enabled }}': 'false'
            '{{ nodejs_version }}': '6'
            '{{ nodejs_enabled }}': 'false'
            '{{ redis_enabled }}': 'false'
            '{{ influxdb_enabled }}': 'false'

YAML
        );
    }

    public function testExecuteWithPhp56()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(['foo-bar.manala', '5.6', "\n", "\n", "\n", "\n", "\n"])
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

        $expectedDeps = <<<'YAML'
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
YAML;

        $this->assertContains($expectedDeps, file_get_contents(self::$cwd.'/ansible/group_vars/app.yml'));

        $expectedBox = <<<'RUBY'
  :name        => 'foo-bar.manala',
  :box         => 'manala/app-dev-debian',
  :box_version => '~> 2.0.0'
RUBY;

        $this->assertContains($expectedBox, file_get_contents(self::$cwd.'/Vagrantfile'));
        $this->assertStringEqualsFile(self::$cwd.'/ansible/.manalize.yml', <<<'YAML'
envs:
    symfony:
        vars:
            '{{ app }}': foo-bar.manala
            '{{ box_version }}': '~> 2.0.0'
            '{{ php_version }}': '5.6'
            '{{ php_enabled }}': 'true'
            '{{ mysql_version }}': '5.6'
            '{{ mysql_enabled }}': 'true'
            '{{ postgresql_version }}': '9.5'
            '{{ postgresql_enabled }}': 'false'
            '{{ mongodb_version }}': '3.2'
            '{{ mongodb_enabled }}': 'false'
            '{{ elasticsearch_version }}': '1.7'
            '{{ elasticsearch_enabled }}': 'false'
            '{{ nodejs_version }}': '6'
            '{{ nodejs_enabled }}': 'false'
            '{{ redis_enabled }}': 'false'
            '{{ influxdb_enabled }}': 'false'

YAML
        );
    }

    public function testExecuteWithDefaultAppNameUsesTheCurrentDirectoryName()
    {
        $tester = new CommandTester(new Setup());
        $tester
            ->setInputs(["\n", "\n", "\n", "\n", "\n", "\n"])
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

        $expectedDeps = <<<'YAML'
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
YAML;
        $this->assertContains($expectedDeps, file_get_contents(self::$cwd.'/ansible/group_vars/app.yml'));

        $expectedBox = <<<'RUBY'
  :name        => 'manalized-app',
  :box         => 'manala/app-dev-debian',
  :box_version => '~> 3.0.0'
RUBY;
        $this->assertContains($expectedBox, file_get_contents(self::$cwd.'/Vagrantfile'));
        $this->assertStringEqualsFile(self::$cwd.'/ansible/.manalize.yml', <<<'YAML'
envs:
    symfony:
        vars:
            '{{ app }}': manalized-app
            '{{ box_version }}': '~> 3.0.0'
            '{{ php_version }}': '7.0'
            '{{ php_enabled }}': 'true'
            '{{ mysql_version }}': '5.6'
            '{{ mysql_enabled }}': 'true'
            '{{ postgresql_version }}': '9.5'
            '{{ postgresql_enabled }}': 'false'
            '{{ mongodb_version }}': '3.2'
            '{{ mongodb_enabled }}': 'false'
            '{{ elasticsearch_version }}': '1.7'
            '{{ elasticsearch_enabled }}': 'false'
            '{{ nodejs_version }}': '6'
            '{{ nodejs_enabled }}': 'false'
            '{{ redis_enabled }}': 'false'
            '{{ influxdb_enabled }}': 'false'

YAML
        );
    }

    public static function tearDownAfterClass()
    {
        (new Filesystem())->remove(MANALIZE_TMP_ROOT_DIR);
    }
}
