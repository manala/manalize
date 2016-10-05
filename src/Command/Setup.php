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

use Manala\Env\Config\Variable\AppName;
use Manala\Env\Config\Variable\Dependency\Dependency;
use Manala\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Env\Config\Variable\MakeTarget;
use Manala\Env\Dumper;
use Manala\Env\EnvEnum;
use Manala\Env\EnvFactory;
use Manala\Env\Metadata\MetadataBag;
use Manala\Env\Metadata\MetadataParser;
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
            ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'One of the supported environment types', 'symfony');
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
        $io->comment(sprintf('Start composing your <info>%s</info> environment', (string) $envType));

        $defaultAppName = strtolower(basename($cwd));
        $appName = $io->ask('Application name', AppName::validate($defaultAppName, false) ? $defaultAppName : 'app', [AppName::class, 'validate']);

        $envMetadata = MetadataParser::parse($envType);
        $env = EnvFactory::createEnv(
            $envType,
            new AppName($appName),
            new MakeTarget('install', $envMetadata->get('script.post_provision')),
            $this->setupDependencies($io, $envMetadata)
        );

        foreach (Dumper::dump($env, $cwd) as $dumpTarget) {
            $io->writeln(sprintf('- %s', str_replace($cwd.'/', '', $dumpTarget)));
        }

        $io->success('Environment successfully configured');

        return 0;
    }

    private function setupDependencies(SymfonyStyle $io, MetadataBag $metadata)
    {
        foreach ($metadata->get('packages') as $name => $settings) {
            $defaultEnabled = $settings['enabled'] ?: false;
            $defaultVersion = isset($settings['default']) ? $settings['default'] : null;
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
}
