<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Manala.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Application extends BaseApplication
{
    const NAME = 'manala/manalize';
    const VERSION = '1.0.0';

    /**
     * @return Application
     */
    public static function create()
    {
        return new self(self::NAME, self::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'setup';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Setup();

        return $commands;
    }

    /**
     * {@inheritdoc}
     *
     * Runs the {@link Setup} command by default so that the cli doesn't expect
     * its name as argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
