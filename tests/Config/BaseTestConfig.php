<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Config;

use Manala\Manalize\Config\Config;

class BaseTestConfig extends \PHPUnit_Framework_TestCase
{
    protected function assertOrigin(Config $config, $name)
    {
        $this->assertSame(realpath(__DIR__.'/../../src/Resources/'.$name), $config->getOrigin());
    }
}
