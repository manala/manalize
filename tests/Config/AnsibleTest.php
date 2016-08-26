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

use Manala\Manalize\Config\Ansible;

class AnsibleTest extends BaseTestConfig
{
    public function testGetPath()
    {
        $ansible = new Ansible();

        $this->assertSame('ansible', $ansible->getPath());
    }

    public function testGetOrigin()
    {
        $this->assertOrigin(new Ansible(), 'ansible');
    }

    public function testGetTemplate()
    {
        $ansible = new Ansible();

        $this->assertSame(realpath(__DIR__.'/../../src/Resources/ansible').'/group_vars/all.yml', $ansible->getTemplate());
    }
}
