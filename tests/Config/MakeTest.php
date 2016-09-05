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

use Manala\Manalize\Config\Make;

class MakeTest extends BaseTestConfig
{
    public function testGetPath()
    {
        $make = new Make($this->getEnvType());

        $this->assertSame('Makefile', $make->getPath());
    }

    public function testGetOrigin()
    {
        $this->assertOrigin(new Make($this->getEnvType()), 'Makefile');
    }

    public function testGetTemplate()
    {
        $make = new Make($this->getEnvType());

        $this->assertNull($make->getTemplate());
    }
}
