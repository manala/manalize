<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env\Config\Variable;

use Manala\Env\Config\Variable\AppVendor;

class AppVendorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReplaces()
    {
        $var = new AppVendor('manala', 'dummy-app');

        $this->assertSame(['{{ vendor }}' => 'manala', '{{ app }}' => 'dummy-app'], $var->getReplaces());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage This value must contain only alphanumeric characters and hyphens
     */
    public function testValidateFailsForNonAlphanumericAndNonHyphenChars()
    {
        AppVendor::validate('dummy_app_with_underscores');
    }

    public function testValidate()
    {
        $this->assertSame('dummy-app', AppVendor::validate('dummy-app'));
    }
}
