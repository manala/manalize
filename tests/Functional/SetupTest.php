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
            ->setInputs(['manala', 'dummy', "\n", "\n", "\n", "\n", "\n", "\n"])
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

        $appVars = file_get_contents(self::$cwd.'/ansible/group_vars/app.yml');
        $this->assertContains('php: true', $appVars);
        $this->assertContains("php_version: '7.0'", $appVars);
        $this->assertContains('nodejs: false', $appVars);
        $this->assertContains("nodejs_version: '6'", $appVars);
        $this->assertContains('mysql: true', $appVars);
        $this->assertContains("mysql_version: '5.6'", $appVars);
        $this->assertContains('mongodb: false', $appVars);
        $this->assertContains("mongodb_version: '3.2'", $appVars);
        $this->assertContains('postgresql: false', $appVars);
        $this->assertContains("postgresql_version: '9.5'", $appVars);
        $this->assertContains('elasticsearch: false', $appVars);
        $this->assertContains("elasticsearch_version: '1.7'", $appVars);
        $this->assertContains('redis: false', $appVars);
        $this->assertContains('influxdb: false', $appVars);
        $this->assertContains(":name        => 'dummy.manala'",  file_get_contents(self::$cwd.'/Vagrantfile'));
    }

    public static function tearDownAfterClass()
    {
        (new Filesystem())->remove(self::$cwd);
    }
}
