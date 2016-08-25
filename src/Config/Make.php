<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Config;

/**
 * Make.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Make extends Config
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return 'Makefile';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return;
    }
}
