<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Command;

use Manala\Manalize\Config\Vars;
use Manala\Manalize\Env\EnvEnum;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Process\Setup as SetupProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Setups a full stack environment on top of Manala' ansible roles.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Setup extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Manalize your application on top of Manala')
            ->addArgument('cwd', InputArgument::OPTIONAL, 'The path of the application to manalize', getcwd())
            ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'One of the available environments', 'symfony-dev');
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

        $io = new SymfonyStyle($input, $output);

        $io->setDecorated(true);
        $io->comment('<info>Start configuring the VM</info>');

        // TODO: ask and validate database name (closes https://github.com/chalasr/manalize/issues/2)

        $vars = new Vars($io->ask('Vendor name', null, [$this, 'validateVar']), $io->ask('App name', null, [$this, 'validateVar']));
        $process = new SetupProcess($cwd);

        $io->comment('<info>Composing your environment on top of Manala</info>');

        foreach ($process->prepare(EnvFactory::createEnv($envType), $vars) as $target => $dump) {
            $io->writeln(sprintf('- %s', str_replace($cwd.DIRECTORY_SEPARATOR, '', $target)));
        }

        $io->newLine();
        $io->comment('<info>Manalizing your application</info>');

        foreach ($process->run() as $buffer) {
            $io->write($buffer);
        }

        if (!$process->isSuccessful()) {
            $io->warning(['An error occured during the process execution', 'Run the command again with the "-v" option for more details']);

            return $process->getExitCode();
        }

        $io->success('Environment successfully created');

        return $process->getExitCode();
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
    public function validateVar($value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new RuntimeException(sprintf('This value must contain only alphanumeric characters and hyphens.', $value));
        }

        return $value;
    }
}
