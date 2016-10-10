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

use GuzzleHttp\Client;
use Manala\Manalize\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Manala self-update command.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class SelfUpdate extends Command
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates manalize to the latest stable version.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ('' === \Phar::running()) {
            $io->error('Self-update is available only for PHAR installation.');

            return 1;
        }

        $this->client = new Client();

        try {
            $latestTag = $this->getLatestTag();
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return 1;
        }

        if ('v'.$this->getApplication()->getVersion() === $latestTag) {
            $io->success(sprintf('manalize is already up to date (%s).', $latestTag));

            return 0;
        }

        try {
            $this->updateBuild($latestTag);
        } catch (\PharException $e) {
            $io->error([
                sprintf('The latest manalize build is corrupted (%s).', $e->getMessage()),
                'Please try to run the command again.',
            ]);

            return 1;
        } catch (\Exception $e) {
            $reason = $e->getMessage();
            $io->error([
                'Unable to download the latest manalize build.'.($reason ? "\nReason: $reason" : ''),
                'Please try to run the command again.',
              ]);

            return 1;
        }

        $io->success(sprintf('manalize successfully updated to %s.', $latestTag));

        return 0;
    }

    private function getLatestTag()
    {
        $response = $this->client->get(
            sprintf('https://api.github.com/repos/%s/releases/latest', Application::REPOSITORY_NAME),
            ['headers' => ['User-Agent' => Application::REPOSITORY_NAME]]
        );

        if (200 !== $response->getStatusCode()) {
            throw new \Exception($response->getReasonPhrase());
        }

        $raw = $response->getBody();
        $release = json_decode($raw, true);

        if (!$raw || !$release) {
            throw new \Exception();
        }

        return $release['tag_name'];
    }

    private function updateBuild($latestTag)
    {
        $localFilename = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-tmp.phar';

        $response = $this->client->get(
            sprintf('https://github.com/%s/releases/download/%s/manalize.phar', Application::REPOSITORY_NAME, $latestTag),
            ['save_to' => $tempFilename]
        );

        if (200 !== $response->getStatusCode()) {
            throw new \Exception($response->getReasonPhrase());
        }

        // Keep the previous build permissions
        chmod($tempFilename, fileperms($localFilename));

        // Check that the phar is valid
        $phar = new \Phar($tempFilename);
        unset($phar);

        // Replace the old build by the new one
        rename($tempFilename, $localFilename);
    }
}
