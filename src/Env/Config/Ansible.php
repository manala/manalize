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
    /**
     * Lazy loads ansible configuration files.
     *
     * {@inheritdoc}
     */
    public function getFiles(): \Generator
    {
        $originDirectory = $this->getOrigin();

        if (!is_readable($originDirectory)) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to load an Ansible configuration from directory "%s" as it is either not readable or doesn\'t exist.',
                $originDirectory
            ));
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($originDirectory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            yield $file;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): \SplFileInfo
    {
        return new \SplFileInfo($this->getOrigin().'/group_vars/app.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return 'ansible';
    }
}
