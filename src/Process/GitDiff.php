<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Process;

use Symfony\Component\Process\Process;

/**
 * Process running `git diff`.
 *
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class GitDiff extends Process
{
    const EXIT_SUCCESS_DIFF = 1;
    const EXIT_SUCCESS_NO_DIFF = 0;

    public function __construct(array $options, string $base, string $target, string $cwd = null)
    {
        $token = implode(' ', $options);

        parent::__construct("git diff $token $base $target", $cwd, null, null, null);
    }

    /**
     * git-diff is also successful if the exit code is `1`.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if ($this->getErrorOutput()) {
            return false;
        }

        return in_array($this->getExitCode(), [static::EXIT_SUCCESS_NO_DIFF, static::EXIT_SUCCESS_DIFF], true);
    }

    public function hasDiff(): bool
    {
        return $this->getExitCode() === static::EXIT_SUCCESS_DIFF;
    }
}
