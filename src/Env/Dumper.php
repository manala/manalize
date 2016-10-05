<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env;

use Manala\Env\Config\Renderer;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Manala environment config dumper.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Dumper
{
    /**
     * Creates and dumps final config files from stubs.
     *
     * @param Env    $env     The whole Config for which to dump the template
     * @param string $workDir
     *
     * @return \Generator
     */
    public static function dump(Env $env, $workDir)
    {
        $fs = new Filesystem();

        foreach ($env->getConfigs() as $config) {
            $baseTarget = $workDir.DIRECTORY_SEPARATOR.$config->getPath();
            $template = $config->getTemplate();

            foreach ($config->getFiles() as $file) {
                $target = str_replace($config->getOrigin(), $baseTarget, $file->getPathName());
                $dump = ((string) $template === $file->getPathname()) ? Renderer::render($config) : file_get_contents($file);
                $fs->dumpFile($target, $dump);

                yield $target;
            }
        }
    }
}
