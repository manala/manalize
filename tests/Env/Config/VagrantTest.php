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

use Manala\Manalize\Env\Config\Vagrant;

class VagrantTest extends BaseTestConfig
{
    public function testGetFiles()
    {
        $this->assertSame($this->getOrigin('manala/Vagrantfile'), (string) (new Vagrant($this->getEnvType()))->getFiles()->current());
    }

    public function testGetTemplate()
    {
        $vagrant = new Vagrant($this->getEnvType());

        $this->assertSame(realpath($this->getOrigin('manala/Vagrantfile').'.twig'), realpath($vagrant->getTemplate()));
    }
}
