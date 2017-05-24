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

use Manala\Manalize\Env\Config\Ansible;
use Manala\Manalize\Env\Config\Make;
use Manala\Manalize\Env\Config\Vagrant;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Package;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use Manala\Manalize\Env\Env;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Env\TemplateName;
use Symfony\Component\Yaml\Yaml;

class EnvFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateEnv()
    {
        $envType = TemplateName::ELAO_SYMFONY();
        $appName = new AppName('rch');
        $boxVersion = new VagrantBoxVersion('~> 3.0.0');
        $env = EnvFactory::createEnv($envType, $appName, $this->prophesize(\Iterator::class)->reveal());
        $expectedConfigs = [new Vagrant($envType, $appName, $boxVersion), Ansible::create($envType, [$appName]), new Make($envType)];

        $this->assertInstanceOf(Env::class, $env);
        $this->assertEquals($expectedConfigs, $env->getConfigs());
        $this->assertCount(3, $env->getConfigs());
    }

    public function testCreateEnvFromManala()
    {
        $manala = <<<MANALA
app:
    name: foo.bar
    template: elao-symfony
system:
    php: 
        version: 7.1
    brainfuck:
        enabled: false
    redis: true
MANALA;

        $expectedTemplateName = TemplateName::ELAO_SYMFONY();
        $expectedAppName = new AppName('foo.bar');
        $expectedPackages = [new Package('php', true, '7.1'), new Package('brainfuck', false), new Package('redis', true)];
        $expectedConfigs = [
            new Vagrant($expectedTemplateName, $expectedAppName, new VagrantBoxVersion()),
            new Ansible($expectedTemplateName, $expectedAppName, ...$expectedPackages),
            new Make($expectedTemplateName),
        ];

        $this->assertEquals($expectedConfigs, EnvFactory::createEnvFromManala(Yaml::parse($manala))->getConfigs());
    }
}
