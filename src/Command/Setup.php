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

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Dependency\Dependency;
use Manala\Manalize\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Manalize\Env\Defaults\Defaults;
use Manala\Manalize\Env\Defaults\DefaultsParser;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvGuesser\ChainEnvGuesser;
use Manala\Manalize\Env\EnvName;
use Manala\Manalize\Exception\HandlingFailureException;
use Manala\Manalize\Handler\Setup as SetupHandler;
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
            ->setDescription('Configures your environment on top of Manala ansible roles')
            ->addArgument('cwd', InputArgument::OPTIONAL, 'The path of the application for which to setup the environment', getcwd())
            ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'One of the supported environment types. Don\'t use this option for building a full custom environment', null)
            ->addOption('no-update', null, InputOption::VALUE_NONE, 'If set, will only update metadata');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = realpath($input->getArgument('cwd'));

        if (!is_dir($cwd)) {
            throw new \RuntimeException(sprintf('The working directory "%s" doesn\'t exist.', $cwd));
        }

        $io = new SymfonyStyle($input, $output);
        $io->setDecorated(true);

        $envName = $this->getEnvName($input);
        if ($envName->is(EnvName::CUSTOM)) {
            $envName = $this->guessEnvName($io, $cwd) ?: $envName;
        }

        $io->comment(sprintf('Start composing your <info>%s</info> environment', (string) $envName));

        $appName = $this->askForAppName($io, strtolower(basename($cwd)));
        $envDefaults = DefaultsParser::parse($envName);
        $options = ['dumper_flags' => $input->getOption('no-update') ? Dumper::DUMP_METADATA : Dumper::DUMP_ALL];

        if ($envName->is(EnvName::CUSTOM) || $this->shouldConfigureDependencies($io, $envDefaults, $envName)) {
            $dependencies = $this->configureDependencies($io, $envDefaults);
        } else {
            $dependencies = SetupHandler::createDefaultDependencySet($envDefaults);
        }

        $handler = new SetupHandler($cwd, new AppName($appName), $envName, $dependencies, $options);

        try {
            $handler->handle(function (string $target) use ($io) {
                $io->writeln(sprintf('- %s', $target));
            }, function (string $target) use ($io, $handler, $cwd) {
                return $this->askStrategyForExistingFile($io, $cwd, $target, $handler->getChoicesForAlreadyExistingFile());
            });
        } catch (HandlingFailureException $e) {
            $io->error(['An error occurred while dumping files:', $e->getMessage()]);

            return 1;
        }

        $io->success('Environment successfully configured');

        return 0;
    }

    private function getEnvName(InputInterface $input): EnvName
    {
        if ($rawName = $input->getOption('env')) {
            if (!EnvName::accepts($rawName)) {
                throw new \UnexpectedValueException(sprintf(
                    'The value for the "--env" option must be one of [%s] (or null for a custom environment), "%s" given.',
                    implode(',', EnvName::values()),
                    $rawName
                ));
            }

            return EnvName::get($rawName);
        }

        return EnvName::CUSTOM();
    }

    private function guessEnvName(SymfonyStyle $io, string $cwd)
    {
        if (!$envName = (new ChainEnvGuesser())->guess(new \SplFileinfo($cwd))) {
            return;
        }

        $io->comment(sprintf(
            "It seems you didn't choose to use one of our built-in environments,\nbut we think that there is one which may be adapted.",
            $envName
        ));

        return $io->confirm(sprintf('Would you like to base your setup on the <comment>%s</comment> environment?', $envName)) ? $envName : null;
    }

    private function configureDependencies(SymfonyStyle $io, Defaults $defaults): \Generator
    {
        foreach ($defaults->get('packages') as $name => $settings) {
            $defaultEnabled = $settings['enabled'] ?: false;
            $defaultVersion = $settings['default'] ?? null;
            $enabled = $settings['required'] ?: $io->confirm(sprintf('Install %s?', $name), $defaultEnabled);

            if (null === $defaultVersion) {
                yield new Dependency($name, $enabled);

                continue;
            }

            if (false === $enabled) {
                yield new VersionBounded($name, $enabled, $defaultVersion);

                continue;
            }

            yield new VersionBounded(
                $name,
                $enabled,
                $this->askForDependencyVersion($io, $name, $settings['constraint'], $defaultVersion)
            );
        }
    }

    private function shouldConfigureDependencies(SymfonyStyle $io, Defaults $defaults, EnvName $envName): bool
    {
        $packages = $defaults->get('packages');

        $io->writeln(sprintf('The default set of dependencies for <info>%s</info> is:', (string) $envName));
        $io->table(['name', 'enabled', 'version'], array_map(function ($name, $package) {
            return [$name, $package['enabled'] ? 'yes' : 'no', $package['default'] ?? '~'];
        }, array_keys($packages), $packages));

        return $io->confirm('Do you want to customize your dependencies?', false);
    }

    private function askStrategyForExistingFile(SymfonyStyle $io, string $cwd, string $target, array $strategies): string
    {
        $strategy = $io->choice(
            sprintf('The file "%s" needs to be updated.', str_replace($cwd.'/', '', $target)),
            array_values($strategies),
            $strategies[Dumper::DO_PATCH]
        );

        return array_search($strategy, $strategies, true);
    }

    private function askForDependencyVersion(SymfonyStyle $io, string $name, string $versionConstraint, string $defaultVersion): string
    {
        return $io->ask(
            sprintf('%s version? (%s)', $name, $versionConstraint),
            $defaultVersion,
            function ($version) use ($versionConstraint) {
                return VersionBounded::validate($version, $versionConstraint);
            }
        );
    }

    private function askForAppName(SymfonyStyle $io, $default): string
    {
        return $io->ask(
            'Application name',
            AppName::validate($default, false) ? $default : 'app', [AppName::class, 'validate']
        );
    }
}
