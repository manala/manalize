<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config;

/**
 * Vagrant configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Vagrant extends Config
{
    public function getFiles(): \Traversable
    {
        yield new \SplFileInfo($this->getOrigin().'/manala/Vagrantfile');
    }
}
