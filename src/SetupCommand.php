<?php

namespace RCH\Manalize;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Setups a full environment on top of manalas.
 * 
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class SetupCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('hello:world')
            ->setDescription('ComponentAlone/Console: Hello world command out of Symfony');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();  

        $files = [
            'Vagrantfile',
            'app/config/parameters.yml.dist',
            'app/config/config.yml',
            'ansible/group_vars/all.yml',
        ];
        $workDir = getcwd();
                
        $fs->mirror(__DIR__.'/ansible', $workDir.'/ansible');
        $fs->copy(__DIR__.'/Makefile', $workDir.'/Makefile');
        $fs->copy(__DIR__.'/Vagrantfile', $workDir.'/Vagrantfile');

        foreach ($files as $file) {
            $io->writeln('- ' . $file);
        }

        if (!$io->confirm('<info>Do you want to continue?</info> [<comment>Y,n</comment>]', true)) {
            return;
        }

        $validator = function ($value) {
            if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
                throw new \InvalidArgumentException('The name should only contains alphanumeric characters (and hyphens)');
            }

            return $value;
        };

        $vendor = $io->ask('<info>Vendor name</info>: ', null, $validator);
        $app = $io->ask('<info>Application name</info> [<comment>app</comment>]: ', 'app', $validator);
        $appDatabase = ($vendor ? $vendor . '_' : '') . $app;
        $appHost = $app . ($vendor ? '.' . $vendor : '');
        $vars = [
            '{{ vendor }}'       => strtolower($vendor),
            '{{ app }}'          => strtolower($app),
            '{{ app_database }}' => strtolower($appDatabase),
            '{{ app_host }}'     => strtolower($appHost),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $content = strtr(file_get_contents($workDir.DIRECTORY_SEPARATOR.$file), $vars);

                file_put_contents($file, $content);
            }
        }

        $appComposerName = ($vendor ? $vendor : $app) . '/' . $app;
        $appName = ($vendor ? str_replace('-', ' ', $vendor) . ' - ' : '') . str_replace('-', ' ', $app);
        
        $builder = new ProcessBuilder(['make', 'setup']);
        $builder->setTimeout(null);

        $io->comment('<info>Manalizing your Symfony application...</info>');
        
        $process = $builder->getProcess();
        $process->run(function ($type, $buffer) {
            print $buffer;
        });

        if (!$process->isSuccessful()) {
            $io->warning(['An error occured during the process execution', 'Run the command again with the "-v" option for more details']);
        }

        $io->success('Manala environment successfully created');

        return $process->getExitCode();
    }
}
