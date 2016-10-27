<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Installer;

class Installer
{
    const DEFAULT_DIR = '/usr/local/bin';

    private $repository;
    private $io;

    public function __construct()
    {
        $this->repository = new ManalizeRepository();
        $this->io = new IO();
    }

    public function install($dir = self::DEFAULT_DIR)
    {
        $io = $this->io;
        try {
            $io->newLine();
            $io->title('Manalize Installer');

            if ('\\' === DIRECTORY_SEPARATOR) {
                throw new \RuntimeException('Sorry, but Windows is not supported right now');
            }

            if (!is_dir($dir) || !is_writeable($dir)) {
                throw new \RuntimeException(sprintf(
                    'Target directory at path "%s" should exist and be writable',
                    $dir
                ));
            }

            $io->newLine();

            $io->note('Retrieving the latest release...');
            $tag = $this->repository->getLatestTag();

            $io->note("Downloading the phar for $tag...");
            $phar = $this->repository->downloadPhar($tag);

            $io->note('Moving executable...');
            $target = "$dir/manalize";
            file_put_contents($target, $phar);

            $io->note('Making Manalize executable...');
            @chmod($target, 0755);

            $io->success("Manalize successfully installed at path \"$target\"");
            $io->newLine();
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            $io->newLine();

            exit(1);
        }

        exit(0);
    }
}

class ManalizeRepository
{
    const GITHUB_RELEASE_PHAR = 'https://github.com/%s/releases/download/%s/manalize.phar';
    const GITHUB_LATEST_RELEASE_URI = 'https://api.github.com/repos/%s/releases/latest';
    const GITHUB_REPOSITORY_NAME = 'manala/manalize';

    private $latestTag;

    public function downloadPhar($tag)
    {
        if (false === $phar = $this->get(sprintf(self::GITHUB_RELEASE_PHAR, self::GITHUB_REPOSITORY_NAME, $tag))) {
            throw new \RuntimeException('Unable to download the phar');
        }

        return $phar;
    }

    public function getLatestTag()
    {
        if (null !== $this->latestTag) {
            return $this->latestTag;
        }

        if (false === $raw = $this->get(sprintf(self::GITHUB_LATEST_RELEASE_URI, self::GITHUB_REPOSITORY_NAME))) {
            throw new \RuntimeException('Unable to retrieve the latest version');
        }

        $release = json_decode($raw, true);

        return $this->latestTag = $release['tag_name'];
    }

    private function get($uri)
    {
        return @file_get_contents(
            $uri,
            null,
            stream_context_create(['http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Manalize (installer)\r\n',
            ]])
        );
    }
}

class IO
{
    private static $availableForegroundColors = [
        'red' => ['set' => 31, 'unset' => 39],
        'cyan' => ['set' => 36, 'unset' => 39],
        'green' => ['set' => 32, 'unset' => 39],
        'yellow' => ['set' => 33, 'unset' => 39],
        'white' => ['set' => 37, 'unset' => 39],
        'default' => ['set' => 39, 'unset' => 39],
    ];

    public function write($message)
    {
        echo $message;
    }

    public function writeln($message)
    {
        $this->write($message);
        $this->newLine();
    }

    public function title($title)
    {
        $underline = str_repeat('=', strlen($title));
        $this->writeColored($title, 'cyan', true);
        $this->writeColored($underline, 'cyan', true);
    }

    public function newLine($count = 1)
    {
        $this->write(str_repeat(PHP_EOL, $count));
    }

    public function success($message)
    {
        $this->newLine();
        $this->writeColored("✔ $message", 'green', true);
    }

    public function error($message)
    {
        $this->newLine();
        $this->writeColored("✘ $message", 'red', true);
    }

    public function note($message)
    {
        $this->writeColored("- $message", 'yellow', true);
    }

    private function writeColored($message, $color, $newLine = false)
    {
        if ($this->hasColorSupport()) {
            $color = self::$availableForegroundColors[$color];
            $message = sprintf("\e[%sm%s\e[%sm", $color['set'], $message, $color['unset']);
        }

        $newLine ? $this->writeln($message) : $this->write($message);
    }

    /**
     * @see \Symfony\Component\Console\Output\StreamOuput
     */
    protected function hasColorSupport()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            return
                '10.0.10586' === PHP_WINDOWS_VERSION_MAJOR.'.'.PHP_WINDOWS_VERSION_MINOR.'.'.PHP_WINDOWS_VERSION_BUILD
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }
}

if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://stdout', 'w'));
}

set_error_handler(
    function ($code, $message, $file, $line) {
        if ($code & error_reporting()) {
            $n = PHP_EOL;
            echo "Error: $message - on line $line$n$n";

            exit(1);
        }
    }
);

$dir = isset($argv[1]) ? $argv[1] : Installer::DEFAULT_DIR;

(new Installer())->install($dir);
