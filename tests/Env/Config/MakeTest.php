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
    public function testGetFiles()
    {
        $this->assertOrigin(new Make($this->getEnvType()), 'manala/make');
    }

    public function testGetTemplate()
    {
        $this->assertSame(realpath($this->getOrigin('manala/make').'/Makefile.vm.twig'), (new Make($this->getEnvType()))->getTemplate()->getPathname());
    }
}
