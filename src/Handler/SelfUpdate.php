<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Handler;

use GuzzleHttp\Client;
use Manala\Manalize\Application;
use Manala\Manalize\Exception\HandlingFailureException;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class SelfUpdate
{
    private $client;
    private $latestTag;
    private $currentBuild;

    public function __construct($currentBuild, Client $client = null)
    {
        $this->currentBuild = $currentBuild;
        $this->client = $client ?: new Client();
    }

    public function handle()
    {
        $tempFilename = manala_get_tmp_dir('manalize_build').'/manalize.temp.phar';

        $response = $this->client->get(
            sprintf('https://github.com/%s/releases/download/%s/manalize.phar', Application::REPOSITORY_NAME, $this->getLatestTag()),
            ['save_to' => $tempFilename]
        );

        if (200 !== $response->getStatusCode()) {
            throw new HandlingFailureException($response->getReasonPhrase());
        }

        // Keep the previous build permissions
        chmod($tempFilename, fileperms($this->currentBuild));

        // Check that the phar is valid
        $phar = new \Phar($tempFilename);
        unset($phar);

        // Replace the old build by the new one
        return rename($tempFilename, $this->currentBuild);
    }

    public function getLatestTag()
    {
        if (null !== $this->latestTag) {
            return $this->latestTag;
        }

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

        return $this->latestTag = $release['tag_name'];
    }
}
