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
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use Manala\Manalize\Env\Env;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Env\EnvName;

class EnvFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateEnv()
    {
        $envType = EnvName::ELAO_SYMFONY();
        $appName = new AppName('rch');
        $boxVersion = new VagrantBoxVersion('~> 3.0.0');
        $env = EnvFactory::createEnv($envType, $appName, $this->prophesize(\Iterator::class)->reveal());
        $expectedConfigs = [new Vagrant($envType, $appName, $boxVersion), new Ansible($envType), new Make($envType)];

        $this->assertInstanceOf(Env::class, $env);
        $this->assertEquals($expectedConfigs, $env->getConfigs());
        $this->assertCount(3, $env->getConfigs());
    }
}
