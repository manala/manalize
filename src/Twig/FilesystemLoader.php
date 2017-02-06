<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Twig;

/**
 * Loads templates through absolute paths in addition of relative ones.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class FilesystemLoader extends \Twig_Loader_Filesystem
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $paths = [], $rootPath = '/')
    {
        parent::__construct([$rootPath, MANALIZE_DIR.'/src/Resources'] + $paths, $rootPath);
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate($name, $throw = true)
    {
        $name = (string) $name;

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (is_file($name)) {
            return $this->cache[$name] = $name;
        }

        return parent::findTemplate($name);
    }
}
