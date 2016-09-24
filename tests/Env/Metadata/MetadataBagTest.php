<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env\Config\Dependency;

use Manala\Env\Metadata\MetadataBag;

class MetadataBagTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $metadata = new MetadataBag(['foo' => ['bar' => 'baz']]);
        $this->assertSame('baz', $metadata->get('foo.bar'));
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Unable to find metadata for path "foo.bab". Did you mean "foo.bar"?
     */
    public function testGetUndefinedPath()
    {
        $metadata = new MetadataBag(['foo' => ['bar' => 'baz']]);
        $metadata->get('foo.bab');
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Unable to find metadata for path "foo.zyo". Possible values: [ foo.bar ]
     */
    public function testGetUndefinedPathWithTooMuchDistance()
    {
        $metadata = new MetadataBag(['foo' => ['bar' => 'baz']]);
        $metadata->get('foo.zyo');
    }
}
