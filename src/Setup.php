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

        $output->setDecorated(true);

        $io->comment('<info>Start creating the VM configuration...</info>');

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

        // TODO: fill the database name in symfony configs (see https://github.com/chalasr/manalize/issues/2)

        foreach ($configs as $config) {
            foreach ($config->getFiles() as $file) {
                $target = str_replace($config->getOrigin(), $config->getTarget(), $file->getPathName());
                $io->writeln(sprintf('- %s', str_replace(getcwd().'/', '', $target)));
                $fs->dumpFile(
                    $target,
                    $config->getTemplate() === $file->getRealPath() ? Dumper::dump($config, $vars) : file_get_contents($file)
                );
            }
        }

        $io->newLine();
        $io->comment('<info>Manalizing your application...</info>');

        return $this->manalize($io);
    }

    private function manalize(OutputInterface $output)
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
