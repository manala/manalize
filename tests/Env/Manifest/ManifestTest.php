<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env\Manifest;

use Manala\Manalize\Env\Manifest\Manifest;

class ManifestTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $defaults = new Manifest(['foo' => ['bar' => 'baz']]);
        $this->assertSame('baz', $defaults->get('foo.bar'));
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Unable to find default for path "foo.bab". Did you mean "foo.bar"?
     */
    public function testGetUndefinedPath()
    {
        $defaults = new Manifest(['foo' => ['bar' => 'baz']]);
        $defaults->get('foo.bab');
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Unable to find default for path "foo.zyo". Possible values: [ foo.bar ]
     */
    public function testGetUndefinedPathWithTooMuchDistance()
    {
        $defaults = new Manifest(['foo' => ['bar' => 'baz']]);
        $defaults->get('foo.zyo');
    }
}
