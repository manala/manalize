<?php

namespace RCH\Manalize;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;
use RCH\Manalize\Config\Ansible;
use RCH\Manalize\Config\Vagrant;
use RCH\Manalize\Config\Makefile;
use RCH\Manalize\Config\Vars;
use RCH\Manalize\Config\Dumper;

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

        $io->comment('Start creating the VM configuration...');

        // TODO: fill the database name here:
        // 'app/config/parameters.yml.dist',
        // 'app/config/config.yml',

        $configs = [
            new Ansible(),
            new Vagrant(),
            new Makefile()
        ];

        $vars = new Vars(
            $io->ask('<info>Vendor name</info>: ', null,  function ($v) { return $this->assertConfigValue($v); }),
            $io->ask('<info>Application name</info> [<comment>app</comment>]: ', 'app', function ($v) { return $this->assertConfigValue($v); })
        );

        $io->comment('<info>Composing your environment on top of Manala...</info>');

        foreach ($configs as $config) {
            // $fs->mirror($origi, $targetDir [, $iterator, $options])
            foreach ($config->getFiles() as $file) {
                $fs->dumpFile(
                    str_replace($config->getOrigin(), $config->getTarget(), $file->getPathName()),
                    $config->getTemplate() === $file->getRealPath() ? Dumper::dump($config, $vars) : file_get_contents($file)
                );
            }
        }

        $io->comment('<info>Manalizing your application...</info>');

        return $this->doManalize($io);
    }

    private function doManalize(OutputInterface $output)
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
    private function assertConfigValue($value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \InvalidArgumentException(sprintf('This value must only contains alphanumeric characters or hyphens, "%s" given.', $value));
        }

        return $value;
    }
}
