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
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class Manifest extends Config
{
    public function __construct()
    {
    }

    public function getVars(): array
    {
        return [
            'app' => [
                'dir' => '/srv/app',
                'log_dir' => '/var/log/app',
                'cache_dir' => '/var/cache/app',
                'sessions_dir' => '/var/lib/app/sessions',
            ],
        ]; // FIXME move to template?
    }
}
