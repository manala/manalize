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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Setups a full stack environment on top of Manala' ansible roles.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Setup extends Command
{
    /**
     * @var string
     */
    protected $workingDirectory;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Manalize your application on top of Manala')
            ->addArgument('work-dir', InputArgument::OPTIONAL, 'The absolute path of the application to manalize');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $workDir = $input->getArgument('work-dir');
        $this->workingDirectory = realpath($workDir);

        if (!is_dir($this->workingDirectory)) {
            throw new \RuntimeException(sprintf('The working directory "%s" doesn\'t exist.', $workDir));
        }

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

        // TODO: ask and validate database name (closes https://github.com/chalasr/manalize/issues/2)

        $io->comment('<info>Composing your environment on top of Manala...</info>');

        foreach ($configs as $config) {
            $this->dumpConfig($config, $vars, $io, $fs);
        }

        $io->newLine();
        $io->comment('<info>Manalizing your application...</info>');

        return $this->manalize($io);
    }

    /**
     * Executes the manalizing process.
     *
     * @param SymfonyStyle $output
     *
     * @return int The process exit code
     */
    protected function manalize(SymfonyStyle $output)
    {
        $builder = new ProcessBuilder(['make', 'setup']);
        $builder
            ->setTimeout(null)
            ->setWorkingDirectory($this->workingDirectory);

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

    /**
     * Dumps all files of a configuration into the working directory.
     *
     * @param Config          $config
     * @param Vars            $vars
     * @param OutputInterface $output
     * @param FileSystem      $fs
     */
    protected function dumpConfig(Config $config, Vars $vars, OutputInterface $output, FileSystem $fs)
    {
        $baseTarget = $this->workingDirectory.DIRECTORY_SEPARATOR.$config->getPath();
        $template = $config->getTemplate();

        foreach ($config->getFiles() as $file) {
            $target = str_replace($config->getOrigin(), $baseTarget, $file->getPathName());
            $dump = ((string) $template === $file->getRealPath()) ? Dumper::dump($config, $vars) : file_get_contents($file);

            $output->writeln(sprintf('- %s', str_replace($this->workingDirectory.DIRECTORY_SEPARATOR, '', $target)));
            $fs->dumpFile($target, $dump);
        }
    }

    /**
     * Checks that a given configuration value is properly formatted.
     *
     * @param string $value The value to assert
     *
     * @return string The validated value
     *
     * @throws \InvalidArgumentException If the value is incorrect
     */
    protected function assertConfigValue($value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \InvalidArgumentException(sprintf('Value "%s" violated a constraint, it must contain only alphanumeric characters or hyphens.', $value));
        }

        return $value;
    }
}
