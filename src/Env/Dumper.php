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
use Manala\Manalize\Env\Config\Manala;
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
    const DUMP_MANALA = 1;
    const DUMP_FILES = 2;
    const DUMP_ALL = 3;

    const DO_REPLACE = 'replace';
    const DO_PATCH = 'patch';
    const DO_NOTHING = 'nothing';

    private $workspace;
    private $patch;
    private $renderer;
    private $fs;

    public function __construct(string $workspace, Renderer $renderer = null)
    {
        $this->workspace = $workspace;
        $this->renderer = $renderer ?: new Renderer();
        $this->fs = new Filesystem();
    }

    /**
     * Creates and dumps final config files from stubs.
     *
     * Note: If a file already exists, it can either be erased, a patch can be created for, or
     * it can be skipped (see Dumper::DO_*).
     *
     * @param Env      $env              The Env for which to dump the rendered config templates
     * @param int      $flags
     * @param callable $conflictCallback A callback returning the strategy to be use in case
     *                                   of existing file
     *
     * @return \Traversable|string[] The dumped file paths
     */
    public function dump(Env $env, int $flags = self::DUMP_ALL, callable $conflictCallback = null): \Traversable
    {
        $templateDir = $env->getBaseDir();

        if (self::DUMP_FILES & $flags) {
            foreach ($env->getConfigs() as $config) {
                yield from $this->dumpFiles($config, $templateDir, $conflictCallback);
            }

            if (null !== $this->patch) {
                yield $this->dumpPatch();

                $this->patch = null;
            }
        }

        if (self::DUMP_MANALA & $flags) {
            yield $this->dumpManala($env);
        }
    }

    private function dumpFiles(Config $config, string $templateDir, callable $conflictCallback = null): \Traversable
    {
        if ($dist = $config->getDist()) {
            $distTarget = "$this->workspace/$dist";
            $this->fs->dumpFile("$distTarget", file_get_contents("$templateDir/dist/$dist"));

            yield $distTarget;
        }

        $template = $config->getTemplate();

        foreach ($config->getFiles() as $file) {
            if ($template && file_exists($template) && in_array((string) $template, [$file->getPathname().'.twig', $file->getPathname()])) {
                $dump = $this->renderer->render($config);
            } else {
                $dump = file_get_contents($file);
            }

            $target = str_replace($templateDir, $this->workspace, 'twig' === $file->getExtension() ? substr($file->getPathname(), 0, -5) : $file->getPathname());

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

    private function dumpManala(Env $env): string
    {
        $manala = [
            'app' => [
                'name' => $env->getAppName()->getValue(),
                'template' => $env->getName(),
            ],
            'system' => [
                'cpus' => 1,
                'memory' => 2048,
            ],
        ];

        foreach ($env->getPackages() as $var) {
            $manala['system'][$var->getName()] = $var->getValue();
        }

        $this->fs->dumpFile($target = "$this->workspace/manala.yml", Yaml::dump($manala, 4));

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
