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

use Manala\Manalize\Env\Config\Ansible;
use Manala\Manalize\Env\Config\Registry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    public function testGetClassesByAliases()
    {
        $this->assertInternalType('array', (new Registry())->getClassesByAliases());
    }

    public function testGetAliasesByClasses()
    {
        $this->assertInternalType('array', (new Registry())->getAliasesByClasses());
    }

    public function testGetClassForAlias()
    {
        $this->assertSame(Ansible::class, (new Registry())->getClassForAlias('ansible'));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Alias "dummy" is not referenced.
     */
    public function testGetClassForAliasThrowsExceptionOnUnknownAlias()
    {
        (new Registry())->getClassForAlias('dummy');
    }

    public function testGetAliasForClass()
    {
        $this->assertSame('ansible', (new Registry())->getAliasForClass(Ansible::class));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Class "stdClass" is not referenced.
     */
    public function testGetAliasForClassThrowsExceptionOnUnknownClass()
    {
        (new Registry())->getAliasForClass('stdClass');
    }
}
