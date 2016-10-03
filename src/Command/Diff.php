<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Command;

use Manala\Env\EnvEnum;
use Manala\Exception\HandlingFailureException;
use Manala\Handler\Diff as DiffHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class Diff extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('diff')
            ->setDescription('Computes the diff between the current project and the Manala templates.')
            ->addArgument('cwd', InputArgument::OPTIONAL, 'The path of the application', getcwd())
            ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'One of the supported environment types', 'symfony')
            ->setHelp(<<<EOTXT
{$this->getDescription()}

The command output can be redirect to a file in order to create a patch to apply later, or for sharing:
<info>
manala diff > my_patch.patch 
</>
You can apply the generated patch by executing one of the following command in the project working directory:
Using <comment>git</>:
<info>
$ manala diff | git apply
$ git apply my_patch.patch
</>
Using <comment>patch</>:
<info>
$ manala diff | patch -p1
$ patch -p1 < my_patch.patch
</>
As the <comment>git diff</> util, the command exit code is 1 if a diff is detected, 0 otherwise.
EOTXT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = realpath($input->getArgument('cwd'));
        $envType = EnvEnum::create($input->getOption('env'));

        if (!is_dir($cwd)) {
            throw new \RuntimeException(sprintf('The working directory "%s" doesn\'t exist.', $cwd));
        }

        $handler = new DiffHandler($envType, $cwd, $output->isDecorated());

        try {
            $handler->handle(function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
        } catch (HandlingFailureException $e) {
            $io = new SymfonyStyle($input, $output);
            $io->error(['An error occurred during the process execution:', $handler->getErrorOutput()]);

            return $handler->getExitCode();
        }

        $errIo = $this->getErrIo($input, $output);

        if (!$handler->hasDiff()) {
            $errIo->success('No diff found.');
        }

        return $handler->getExitCode();
    }

    private function getErrIo(InputInterface $input, OutputInterface $output)
    {
        if (!$output instanceof ConsoleOutput) {
            return new SymfonyStyle($input, new NullOutput());
        }

        $errIo = new SymfonyStyle($input, $output->getErrorOutput());
        $errIo->setDecorated(!$input->getOption('no-ansi') || $input->getOption('ansi'));

        return $errIo;
    }
}
