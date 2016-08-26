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

use Manala\Manalize\Config\Vagrant;

class VagrantTest extends BaseTestConfig
{
    public function testGetPath()
    {
        $vagrant = new Vagrant();

        $this->assertSame('Vagrantfile', $vagrant->getPath());
    }

    public function testGetOrigin()
    {
        $this->assertOrigin(new Vagrant(), 'Vagrantfile');
    }

    public function testGetTemplate()
    {
        $vagrant = new Vagrant();

        $this->assertSame(realpath(__DIR__.'/../../src/Resources/Vagrantfile'), $vagrant->getTemplate());
    }
}
