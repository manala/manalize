<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env\Config;

use Manala\Manalize\Env\Config\Make;

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

        $this->assertInstanceOf(\SplFileInfo::class, $make->getTemplate());
    }
}
