<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Template;

use Manala\Manalize\Process\GitCheckout;
use Manala\Manalize\Process\GitClone;
use Manala\Manalize\Process\GitFetch;
use Manala\Manalize\Process\GitRevList;
use Manala\Manalize\Process\GitRevParse;
use Symfony\Component\Filesystem\Filesystem;

final class Syncer
{
    private $templateDir;
    private $fs;
    private $latestRevision;
    private $repository;

    const DEFAULT_REPOSITORY = 'https://github.com/manala/manalize-templates';

    public function __construct($templateDir = null, $repository = self::DEFAULT_REPOSITORY)
    {
        $this->templateDir = $templateDir ?: MANALIZE_HOME.'/templates';
        $this->fs = new Filesystem();
        $this->repository = $repository;
    }

    public function sync($revision = null)
    {
        if (!is_dir($this->templateDir)) {
            (new GitClone($this->repository, $this->templateDir))->run();
        }

        if (null === $revision) {
            $revision = $this->getLastRevision();
        }

        if ($this->isFresh($revision)) {
            return;
        }

        return (new GitCheckout($revision, $this->templateDir, true))->run();
    }

    public function getTemplateDir(): string
    {
        return $this->templateDir;
    }

    private function isFresh($revision): bool
    {
        $process = new GitRevParse($this->templateDir);
        $process->run();

        return $process->getOutput() === $revision;
    }

    private function getLastRevision(): string
    {
        if (null !== $this->latestRevision) {
            return $this->latestRevision;
        }

        (new GitFetch($this->templateDir))->run();

        $getLatestHash = new GitRevList($this->templateDir);
        $getLatestHash->run();

        return $this->latestRevision = $getLatestHash->getOutput();
    }
}
