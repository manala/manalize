<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env\Config\Variable;

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\VariableExtractor;
use PHPUnit\Framework\TestCase;

class VariableExtractorTest extends TestCase
{
    public function testExtract()
    {
        $this->assertSame(['value' => 'manala'], (new VariableExtractor())->extract(new AppName('manala')));
    }
}
