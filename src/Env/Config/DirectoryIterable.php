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

trait DirectoryIterable
{
    public function getIterator(\SplFileInfo $directory): \Traversable
    {
        if (!is_readable($directory)) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to load an Ansible configuration from directory "%s" as it is either not readable or doesn\'t exist.',
                $directory
            ));
        }

        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }
}
