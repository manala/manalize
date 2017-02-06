<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Handler;

use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Env\EnvName;
use Manala\Manalize\Exception\HandlingFailureException;
use Manala\Manalize\Process\GitDiff;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class Diff
{
    private $envName;
    private $cwd;
    private $fs;
    private $colorSupport;

    /**
     * @param string       $cwd          The working dir
     * @param bool         $colorSupport
     * @param EnvName|null $envName
     */
    public function __construct(EnvName $envName, string $cwd, bool $colorSupport = true)
    {
        $this->envName = $envName;
        $this->cwd = $cwd;
        $this->colorSupport = $colorSupport;

        $this->fs = new Filesystem();
    }

    public function handle(callable $notifier, callable $noDiffNotifier = null): int
    {
        $resourcesPath = $this->createTmpEnv();
        $diffOptions = [
            '--diff-filter=d',
            '--no-index',
            '--patch',
            $this->colorSupport ? '--color' : '--no-color',
        ];

        $process = new GitDiff($diffOptions, '.', $resourcesPath, $this->cwd);
        $process->run(function ($type, $buffer) use ($resourcesPath, $notifier) {
            $diff = strtr($buffer, [
                "b$resourcesPath" => 'b',
                "a$resourcesPath" => 'a',
                'a/./' => 'a/',
                'b/./' => 'b/',
            ]);

            $notifier($diff);
        });

        if (!$process->isSuccessful()) {
            throw new HandlingFailureException($process->getErrorOutput());
        }

        $this->fs->remove($resourcesPath);

        if (!$process->hasDiff() && $noDiffNotifier) {
            $noDiffNotifier();
        }

        return $process->getExitCode();
    }

    private function createTmpEnv(): string
    {
        $tmpPath = manala_get_tmp_dir('diff_');

        $this->fs->mkdir($tmpPath);

        $dumper = new Dumper($tmpPath);
        $metadata = Yaml::parse(file_get_contents("$this->cwd/ansible/.manalize.yml"));

        for (
            $dump = $dumper->dump(EnvFactory::createEnvFromMetadata($metadata, $this->envName->getValue()), Dumper::DUMP_FILES);
            $dump->valid();
            $dump->next()
        );

        return $tmpPath;
    }
}
