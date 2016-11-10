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
use Manala\Manalize\Env\EnvEnum;
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
            ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'One of the supported environment types', 'symfony')
            ->addOption('no-update', null, InputOption::VALUE_NONE, 'If set, will only update metadata')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = realpath($input->getArgument('cwd'));
        $envName = EnvEnum::create($input->getOption('env'));

        if (!is_dir($cwd)) {
            throw new \RuntimeException(sprintf('The working directory "%s" doesn\'t exist.', $cwd));
        }

        $io = new SymfonyStyle($input, $output);
        $io->setDecorated(true);
        $io->comment(sprintf('Start composing your <info>%s</info> environment', (string) $envName));

        $defaultAppName = strtolower(basename($cwd));
        $appName = $io->ask('Application name', AppName::validate($defaultAppName, false) ? $defaultAppName : 'app', [AppName::class, 'validate']);

        $envMetadata = DefaultsParser::parse($envName);
        $options = ['dumper_flags' => $input->getOption('no-update') ? Dumper::DUMP_METADATA : Dumper::DUMP_ALL];
        $dependencies = $this->shouldConfigureDependencies($io, $envMetadata, $envName)
            ? $this->configureDependencies($io, $envMetadata, $envName)
            : SetupHandler::createDefaultDependencySet($envMetadata);

        $handler = new SetupHandler($cwd, new AppName($appName), $envName, $dependencies, $options);
        $handler->handle(function ($target) use ($io) {
            $io->writeln(sprintf('- %s', $target));
        });

        $io->success('Environment successfully configured');

        return 0;
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

            $versionConstraint = $settings['constraint'];
            $requiredVersion = $io->ask(
                sprintf('%s version? (%s)', $name, $versionConstraint),
                $defaultVersion,
                function ($version) use ($versionConstraint) {
                    return VersionBounded::validate($version, $versionConstraint);
                }
            );

            yield new VersionBounded($name, $enabled, $requiredVersion);
        }
    }

    private function shouldConfigureDependencies(SymfonyStyle $io, Defaults $defaults, EnvEnum $envName): bool
    {
        $packages = $defaults->get('packages');

        $io->writeln(sprintf('The default set of dependencies for <info>%s</info> is:', (string) $envName));
        $io->table(['name', 'enabled', 'version'], array_map(function ($name, $package) {
            return [$name, $package['enabled'] ? 'yes' : 'no', $defaults['default'] ?? '~'];
        }, array_keys($packages), $packages));

        return $io->confirm('Do you want to customize your dependencies?', false);
    }
}
