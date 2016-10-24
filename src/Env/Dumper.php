<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env;

use Manala\Manalize\Env\Config\Renderer;
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
     * @param Env    $env     The Env for which to dump the rendered config templates
     * @param string $workDir The manalized project directory
     *
     * @return \Generator The dumped file paths
     */
    public static function dump(Env $env, $workDir)
    {
        foreach ($env->getConfigs() as $config) {
            $baseTarget = "$workDir/{$config->getPath()}";
            $template = $config->getTemplate();

            foreach ($config->getFiles() as $file) {
                $target = str_replace($config->getOrigin(), $baseTarget, $file->getPathName());
                $dump = $file->getPathname() === (string) $template ? Renderer::render($config) : file_get_contents($file);
                (new Filesystem())->dumpFile($target, $dump);

                yield $target;
            }
        }
    }

    /**
     * Dumps the metadata into a file.
     *
     * @param Env    $env     The Env for which to dump the metadata
     * @param string $workDir The manalized project directory
     *
     * @return string The metadata file path
     */
    public static function dumpMetadata(Env $env, $workDir)
    {
        (new Filesystem())->dumpFile($target = "$workDir/ansible/.manalize", serialize($env));

        return $target;
    }
}
