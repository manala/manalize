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
 * Make.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Make extends Config
{
    use DirectoryIterable;

    /**
     * {@inheritdoc}
     */
    public function getOrigin(): \SplFileInfo
    {
        return new \SplFileInfo(parent::getOrigin().'/manala/make');
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(): \Traversable
    {
        yield from $this->getIterator($this->getOrigin());
    }

    public function getDist()
    {
        return 'Makefile';
    }

    public function getTemplate()
    {
        return new \SplFileInfo($this->getOrigin().'/Makefile.vm.twig');
    }
}
