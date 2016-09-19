<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Config;

use Manala\Config\Config;
use Manala\Env\EnvEnum;

class BaseTestConfig extends \PHPUnit_Framework_TestCase
{
    const ENV = EnvEnum::SYMFONY_DEV;

    protected function assertOrigin(Config $config, $name)
    {
        $this->assertSame(realpath($this->getOrigin($name)), $config->getOrigin());
    }

    protected function getEnvType()
    {
        return EnvEnum::create(self::ENV);
    }

    protected function getOrigin($name)
    {
        return __DIR__.'/../../src/Resources/'.self::ENV.'/'.$name;
    }
}
