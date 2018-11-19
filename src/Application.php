<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize;

use Symfony\Component\Console\Application as BaseApplication;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class Application extends BaseApplication
{
    const VERSION = '0.9.2-DEV';
    const REPOSITORY_NAME = 'manala/manalize';

    public function __construct()
    {
        parent::__construct('Manalize', self::VERSION);

        $this->addCommands([
            new Command\Setup(),
            new Command\Diff(),
            new Command\CheckRequirements(),
            new Command\SelfUpdate(),
        ]);
    }
}
