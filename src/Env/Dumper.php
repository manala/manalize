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

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Config\Renderer;
use Manala\Manalize\Exception\FailedDumpException;
use Manala\Manalize\Process\GitDiff;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Manala environment config dumper.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Dumper
{
    const DUMP_METADATA = 1;
    const DUMP_FILES = 2;
    const DUMP_ALL = 3;

    const DO_REPLACE = 'replace';
    const DO_PATCH = 'patch';
    const DO_NOTHING = 'nothing';

    private $workspace;
    private $patch;
    private $fs;

    public function __construct(string $workspace)
    {
        $this->workspace = $workspace;
        $this->fs = new Filesystem();
    }

    /**
     * Creates and dumps final config files from stubs.
     *
     * Note: If a file already exists, it can either be erased, a patch can be created for, or
     * it can be skipped.
     *
     * @param Env      $env              The Env for which to dump the rendered config templates
     * @param int      $flags
     * @param callable $conflictCallback A callback returning the strategy to be use in case
     *                                   of existing file
     *
     * @return \Generator The dumped file paths
     */
    public function dump(Env $env, int $flags = self::DUMP_ALL, callable $conflictCallback = null): \Generator
    {
        if (self::DUMP_FILES & $flags) {
            foreach ($env->getConfigs() as $config) {
                yield from $this->dumpFiles($config, $conflictCallback);
            }

            if (null !== $this->patch) {
                yield $this->dumpPatch();
            }
        }

        if (self::DUMP_METADATA & $flags) {
            yield $this->dumpMetadata($env);
        }
    }

    private function dumpFiles(Config $config, callable $conflictCallback = null): \Generator
    {
        $baseTarget = "$this->workspace/{$config->getPath()}";
        $template = $config->getTemplate();

        foreach ($config->getFiles() as $file) {
            $target = str_replace($config->getOrigin(), $baseTarget, $file->getPathName());
            $dump = $file->getPathname() === (string) $template ? Renderer::render($config) : file_get_contents($file);

            if ($conflictCallback && $this->fs->exists($target) && $dump !== file_get_contents($target)) {
                $strategy = $conflictCallback($target);

                if (self::DO_NOTHING === $strategy) {
                    continue;
                }

                if (self::DO_PATCH === $strategy) {
                    $this->createPatch($target, $dump);

                    continue;
                }
            }

            $this->fs->dumpFile($target, $dump);

            yield $target;
        }
    }

    private function dumpMetadata(Env $env): string
    {
        (new Filesystem())->dumpFile(
            $target = "$this->workspace/ansible/.manalize.yml",
            Yaml::dump((new EnvExporter())->export($env), 4)
        );

        return $target;
    }

    private function dumpPatch(): string
    {
        $path = "$this->workspace/manalize.patch";

        if (!$this->patch) {
            throw new FailedDumpException('Cannot dump an empty patch.');
        }

        $this->fs->dumpFile($path, $this->patch);

        return $path;
    }

    private function createPatch(string $path, string $dump)
    {
        $tempDir = manala_get_tmp_dir('dump_');
        $tempDump = str_replace($this->workspace, $tempDir, $path);

        $this->fs->dumpFile($tempDump, $dump);

        $process = new GitDiff(['--no-index', '--patch', '--no-color'], $path, $tempDump);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new FailedDumpException($process->getErrorOutput());
        }

        $this->fs->remove($tempDir);

        if (null === $this->patch) {
            $this->patch = '';
        }

        $this->patch .= strtr($process->getOutput(), ["a$this->workspace" => 'a', "b$tempDir" => 'b']);
    }
}
