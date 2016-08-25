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

use Manala\Manalize\Config\Ansible;
use Manala\Manalize\Config\Config;
use Manala\Manalize\Config\Dumper;
use Manala\Manalize\Config\Make;
use Manala\Manalize\Config\Vagrant;
use Manala\Manalize\Config\Vars;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Setups a full environment on top of Manala' ansible roles.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Setup extends Command
{
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Manalize your application on top of Manala');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $io->setDecorated(true);
        $io->comment('<info>Start creating the VM configuration...</info>');

        $configs = [new Ansible(), new Vagrant(), new Make()];
        $vars = new Vars(
            $io->ask('<info>Vendor name</info>: ', null,  function ($v) {
                return $this->assertConfigValue($v);
            }),
            $io->ask('<info>Application name</info> [<comment>app</comment>]: ', 'app', function ($v) {
                return $this->assertConfigValue($v);
            })
        );

        $io->comment('<info>Composing your environment on top of Manala...</info>');

        foreach ($configs as $config) {
            $this->dumpConfig($config, $vars, $io, $fs);
        }

        // TODO: fill the database name in symfony configs (see https://github.com/chalasr/manalize/issues/2)

        $io->newLine();
        $io->comment('<info>Manalizing your application...</info>');

        return $this->manalize($io);
    }

    protected function manalize(OutputInterface $output)
    {
        $builder = new ProcessBuilder(['make', 'setup']);
        $builder->setTimeout(null);

        $process = $builder->getProcess();
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $output->warning(['An error occured during the process execution', 'Run the command again with the "-v" option for more details']);
        }

        $output->success('Environment successfully created');

        return $process->getExitCode();
    }

    protected function dumpConfig(Config $config, Vars $vars, OutputInterface $output, FileSystem $fs = null)
    {
        $baseOrigin = $config->getOrigin();
        $baseTarget = $config->getTarget();
        $template = $config->getTemplate();

        foreach ($config->getFiles() as $file) {
            $target = str_replace($baseOrigin, $baseTarget, $file->getPathName());
            $dump = ((string) $template === $file->getRealPath()) ? Dumper::dump($config, $vars) : file_get_contents($file);

            $output->writeln(sprintf('- %s', str_replace(getcwd().'/', '', $target)));
            $fs->dumpFile($target, $dump);
        }
    }

    /**
     * Checks that a given configuration value is properly formatted.
     *
     * @param string $value The value to assert
     * @param string $key   The key for which to assert the value
     *
     * @return string The validated value
     *
     * @throws \InvalidArgumentException If the value is incorrect
     */
    protected function assertConfigValue($value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \InvalidArgumentException(sprintf('This value must only contains alphanumeric characters or hyphens, "%s" given.', $value));
        }

        return $value;
    }
}
