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

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\TemplateName;

class BaseTestConfig extends \PHPUnit_Framework_TestCase
{
    const ENV = TemplateName::ELAO_SYMFONY;

    protected function assertOrigin(Config $config, $name)
    {
        $this->assertSame(realpath($this->getOrigin($name)), realpath($config->getOrigin()));
    }

    protected function getEnvType()
    {
        return TemplateName::get(self::ENV);
    }

    protected function getOrigin($name)
    {
        return MANALIZE_HOME.'/templates/'.self::ENV.'/'.$name;
    }
}
