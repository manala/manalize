<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env;

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvExporter;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Env\EnvName;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class DumperTest extends TestCase
{
    private static $cwd;

    public function setUp()
    {
        self::$cwd = manala_get_tmp_dir('dumper_test_');
        (new Filesystem())->mkdir(self::$cwd);
    }

    public function testDump()
    {
        list($env, $cwd) = $this->createEnv();

        foreach ((new Dumper($cwd))->dump($env) as $̄);

        $this->assertFileExists("$cwd/ansible/ansible.yml");
        $this->assertStringEqualsFile("$cwd/ansible/.manalize.yml", Yaml::dump((new EnvExporter())->export($env), 4));
    }

    public function testDumpMetadataOnly()
    {
        list($env, $cwd) = $this->createEnv();

        foreach ((new Dumper($cwd))->dump($env, Dumper::DUMP_METADATA) as $̄);

        $this->assertFileNotExists("$cwd/ansible/ansible.yml");
        $this->assertFileExists("$cwd/ansible/.manalize.yml");
    }

    public function testDumpFilesOnly()
    {
        list($env, $cwd) = $this->createEnv();

        foreach ((new Dumper($cwd))->dump($env, Dumper::DUMP_FILES) as $̄);

        $this->assertFileExists("$cwd/ansible/ansible.yml");
        $this->assertFileNotExists("$cwd/ansible/.manalize.yml");
    }

    public function tearDown()
    {
        (new Filesystem())->remove(self::$cwd);
    }

    private function createEnv()
    {
        $baseOrigin = self::$cwd;
        @mkdir("$baseOrigin/dummy");
        file_put_contents("$baseOrigin/dummy/dummyconf", 'FooBar');

        $config = $this->prophesize(Config::class);
        $config
            ->getPath()
            ->willReturn('dummy');
        $config
            ->getOrigin()
            ->willReturn("$baseOrigin/dummy");
        $config
            ->getFiles()
            ->willReturn(["$baseOrigin/dummy/dummyconf"]);
        $config
            ->getTemplate()
            ->willReturn(null);

        $env = EnvFactory::createEnv(
            EnvName::SYMFONY(),
            new AppName('dummy'),
            $this->prophesize(\Iterator::class)->reveal()
        );

        $cwd = "$baseOrigin/target";
        @mkdir($cwd);

        return [$env, $cwd];
    }
}
