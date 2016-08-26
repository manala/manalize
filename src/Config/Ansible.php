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
 * Object representation of an Ansible configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Ansible extends Config
{
    /**
     * Lazy loads ansible configuration files.
     *
     * {@inheritdoc}
     */
    public function getFiles()
    {
        $originDirectory = $this->getOrigin();

        if (!is_readable($originDirectory)) {
            throw new \InvalidArgumentException('Unable to load an Ansible configuration from directory "%s" as it is either not readable or doesn\'t exist.');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($originDirectory, \FilesystemIterator::SKIP_DOTS),
             \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            yield $file;
        }
    }

    public function getTemplate()
    {
        return $this->getOrigin().'/group_vars/all.yml';
    }

    public function getPath()
    {
        return 'ansible';
    }
}
