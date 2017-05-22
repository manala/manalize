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
 * Object representation of an Ansible configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Ansible extends Config
{
    use DirectoryIterable;

    /**
     * Lazy loads ansible configuration files.
     *
     * {@inheritdoc}
     */
    public function getFiles(): \Traversable
    {
       yield from $this->getIterator($this->getOrigin());
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): \SplFileInfo
    {
        return new \SplFileInfo($this->getOrigin().'/group_vars/app.yml.twig');
    }

    public function getOrigin(): \SplFileInfo
    {
        return new \SplFileInfo(parent::getOrigin().'/ansible');
    }
}
