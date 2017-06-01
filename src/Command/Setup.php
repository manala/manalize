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
use Manala\Manalize\Env\Config\Variable\Package;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvGuesser\ChainEnvGuesser;
use Manala\Manalize\Env\Manifest\Manifest;
use Manala\Manalize\Env\Manifest\ManifestLoader;
use Manala\Manalize\Env\TemplateName;
use Manala\Manalize\Exception\InvalidConfigurationException;
use Manala\Manalize\Handler\Setup as SetupHandler;
use Manala\Manalize\Template\Syncer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

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
            ->addOption('template', null, InputOption::VALUE_OPTIONAL, 'One of the supported templates. Don\'t use this option for building a full custom environment', null)
            ->addOption('no-update', null, InputOption::VALUE_NONE, 'If set, will only update metadata')
        ;
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

        $template = $this->getTemplateName($input, $cwd);
        if ($template->is(TemplateName::CUSTOM)) {
            $template = $this->guessTemplateName($io, $cwd) ?: $template;
        }

        try {
            $syncer = new Syncer();
            $syncer->sync('bcae854');
        } catch (\Throwable $e) {
            $io->error('An error occured while syncing templates: '.$e->getMessage());

            return 1;
        }

        $io->comment(sprintf('Start composing your <info>%s</info> environment', (string) $template));

        $appName = $this->askForAppName($io, strtolower(basename($cwd)));
        $envManifest = (new ManifestLoader($template))->load();
        $options = ['dumper_flags' => $input->getOption('no-update') ? Dumper::DUMP_MANALA : Dumper::DUMP_ALL];

        if ($template->is(TemplateName::CUSTOM) || $this->shouldConfigurePackages($io, $envManifest, $template)) {
            $packages = $this->configurePackages($io, $envManifest);
        } else {
            $packages = SetupHandler::createDefaultPackageSet($envManifest);
        }

        $handler = new SetupHandler($cwd, new AppName($appName), $template, $packages, $options);
        $handler->handle(function (string $target) use ($io) {
            $io->writeln(sprintf('- %s', $target));
        }, function (string $target) use ($io, $handler, $cwd) {
            return $this->askStrategyForExistingFile($io, $cwd, $target, $handler->getChoicesForAlreadyExistingFile());
        });

        $io->success('Environment successfully configured');

        return 0;
    }

    private function getTemplateName(InputInterface $input, string $cwd): TemplateName
    {
        if ($rawName = $input->getOption('template')) {
            if (!TemplateName::accepts($rawName)) {
                throw new \UnexpectedValueException(sprintf(
                    'The value for the "--template" option must be one of [%s] (or null for a custom environment), "%s" given.',
                    implode(',', TemplateName::values()),
                    $rawName
                ));
            }

            return TemplateName::get($rawName);
        }

        if (is_readable($dotfile = $cwd.'/manala.yaml')) {
            $rawConfig = Yaml::parse(file_get_contents($dotfile));

            if (!isset($rawConfig['template'])) {
                throw new InvalidConfigurationException("The $dotfile file must contain a \"template\" key.");
            }

            if (!TemplateName::accepts($templateName = $rawConfig['template']['name'])) {
                throw new InvalidConfigurationException(sprintf('There is no env called "%s". Possilble envs are [%s]', $templateName, implode(', ', TemplateName::values())));
            }

            return TemplateName::get($templateName);
        }

        return TemplateName::CUSTOM();
    }

    private function guessTemplateName(SymfonyStyle $io, string $cwd)
    {
        if (!$template = (new ChainEnvGuesser())->guess(new \SplFileinfo($cwd))) {
            return;
        }

        $io->comment(sprintf(
            "It seems you didn't choose to use one of our built-in environments,\nbut we think that there is one which may be adapted.",
            $template
        ));

        return $io->confirm(sprintf('Would you like to base your setup on the <comment>%s</comment> environment?', $template)) ? $template : null;
    }

    private function configurePackages(SymfonyStyle $io, Manifest $defaults): \Generator
    {
        foreach ($defaults->get('packages') as $name => $settings) {
            $defaultEnabled = $settings['enabled'] ?: false;
            $defaultVersion = $settings['default'] ?? null;
            $enabled = $settings['required'] ?: $io->confirm(sprintf('Install %s?', $name), $defaultEnabled);

            if (null === $defaultVersion) {
                yield new Package($name, $enabled);

                continue;
            }

            if (false === $enabled) {
                yield new Package($name, $enabled, $defaultVersion);

                continue;
            }

            yield new Package($name, $enabled, $this->askForPackageVersion($io, $name, $settings['constraint'], $defaultVersion));
        }
    }

    private function shouldConfigurePackages(SymfonyStyle $io, Manifest $defaults, TemplateName $template): bool
    {
        $packages = $defaults->get('packages');

        $io->writeln(sprintf('The default set of packages for <info>%s</info> is:', (string) $template));
        $io->table(['name', 'enabled', 'version'], array_map(function ($name, $package) {
            return [$name, $package['enabled'] ? 'yes' : 'no', $package['default'] ?? '~'];
        }, array_keys($packages), $packages));

        return $io->confirm('Do you want to customize your packages?', false);
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

    private function askForPackageVersion(SymfonyStyle $io, string $name, string $versionConstraint, string $defaultVersion): string
    {
        return $io->ask(
            sprintf('%s version? (%s)', $name, $versionConstraint),
            $defaultVersion,
            function ($version) use ($versionConstraint) {
                return Package::validate($version, $versionConstraint);
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
