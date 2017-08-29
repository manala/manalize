<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Command;

use Manala\Manalize\Exception\HandlingFailureException;
use Manala\Manalize\Handler\SelfUpdate as SelfUpdateHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Manala self-update command.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class SelfUpdate extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates manalize to the latest stable version.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ('' === \Phar::running()) {
            $io->error('Self-update is available only for PHAR installation.');

            return 1;
        }

        $handler = new SelfUpdateHandler(realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0]);

        try {
            $latestTag = $handler->getLatestTag();
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return 1;
        }

        if ('v'.$this->getApplication()->getVersion() === $latestTag) {
            $io->success(sprintf('manalize is already up to date (%s).', $latestTag));

            return 0;
        }

        try {
            $handler->handle();
        } catch (HandlingFailureException $e) {
            $reason = $e->getMessage();
            $io->error([
                'Unable to download the latest manalize build.'.($reason ? "\nReason: $reason" : ''),
                'Please try to run the command again.',
              ]);

            return 1;
        } catch (\PharException $e) {
            $io->error([
                sprintf('The latest manalize build is corrupted (%s).', $e->getMessage()),
                'Please try to run the command again.',
            ]);

            return 1;
        }

        $io->success(sprintf('manalize successfully updated to %s.', $latestTag));

        return 0;
    }
}
