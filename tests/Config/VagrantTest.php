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

use Manala\Config\Vagrant;

class VagrantTest extends BaseTestConfig
{
    public function testGetPath()
    {
        $vagrant = new Vagrant($this->getEnvType());

        $this->assertSame('Vagrantfile', $vagrant->getPath());
    }

    public function testGetOrigin()
    {
        $this->assertOrigin(new Vagrant($this->getEnvType()), 'Vagrantfile');
    }

    public function testGetTemplate()
    {
        $vagrant = new Vagrant($this->getEnvType());

        $this->assertSame(realpath($this->getOrigin('Vagrantfile')), $vagrant->getTemplate());
    }
}
