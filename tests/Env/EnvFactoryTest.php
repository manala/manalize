<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env;

use Manala\Manalize\Config\Ansible;
use Manala\Manalize\Config\Make;
use Manala\Manalize\Config\Vagrant;
use Manala\Manalize\Env\Env;
use Manala\Manalize\Env\EnvEnum;
use Manala\Manalize\Env\EnvFactory;

class EnvFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateEnv()
    {
        $envType = EnvEnum::create(EnvEnum::SYMFONY_DEV);
        $env = EnvFactory::createEnv($envType);
        $expectedConfigs = [new Ansible($envType), new Vagrant($envType), new Make($envType)];

        $this->assertInstanceOf(Env::class, $env);
        $this->assertEquals($expectedConfigs, $env->getConfigs());
        $this->assertCount(3, $env->getConfigs());
    }
}
