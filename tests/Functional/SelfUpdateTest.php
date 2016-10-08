<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Functional;

use Manala\Manalize\Application;
use Manala\Manalize\Command\Setup;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class SelfUpdateTest extends \PHPUnit_Framework_TestCase
{
    private static $cwd;

    public function setUp()
    {
        $cwd = manala_get_tmp_dir('tests_selfupdate_');
        $fs = new Filesystem();

        if ($fs->exists($cwd)) {
            $fs->remove($cwd);
        }

        $fs->mkdir($cwd);

        self::$cwd = $cwd;
    }

    public function testExecute()
    {
        $process = new Process('make build', MANALIZE_DIR);
        $process->run();

        if (!$process->isSuccessful() && false !== strpos($process->getErrorOutput(), 'box: command not found')) {
            $this->markTestSkipped('"kherge/box" is required and should be in your $PATH in order to build the phar.');
        }

        chmod(MANALIZE_DIR.'/manalize.phar', 0777);
        rename(MANALIZE_DIR.'/manalize.phar', self::$cwd.'/manalize.phar');

        $latestBuild = json_decode(file_get_contents(
            sprintf('https://api.github.com/repos/%s/releases/latest', Application::REPOSITORY_NAME),
            null,
            stream_context_create(['http' => ['header' => 'User-Agent: '.Application::REPOSITORY_NAME]])
        ), true);
        $latestTag = $latestBuild['tag_name'];

        $process = new Process('php '.self::$cwd.'/manalize.phar self-update');
        $process->setTimeout(null)->run();

        if (!$process->isSuccessful()) {
            echo $process->getErrorOutput();
        }

        $this->assertTrue($process->isSuccessful());

        if ('v'.Application::VERSION === $latestTag) {
            return $this->assertContains("manalize is already up to date ($latestTag)", $process->getOutput());
        }

        $this->assertContains("manalize successfully updated to $latestTag", $process->getOutput());
    }

    public function testExecuteWithAlreadyUpToDateBuild()
    {
        $latestBuild = json_decode(file_get_contents(
            sprintf('https://api.github.com/repos/%s/releases/latest', Application::REPOSITORY_NAME),
            null,
            stream_context_create(['http' => ['header' => 'User-Agent: '.Application::REPOSITORY_NAME]])
        ), true);

        $latestTag = $latestBuild['tag_name'];

        file_put_contents(
            self::$cwd.'/manalize.phar',
            file_get_contents(sprintf('https://github.com/%s/releases/download/%s/manalize.phar', Application::REPOSITORY_NAME, $latestTag))
        );

        $process = new Process('php '.self::$cwd.'/manalize.phar self-update');
        $process->setTimeout(null)->run();

        if (!$process->isSuccessful()) {
            if (strpos($process->getErrorOutput(), 'Command "self-update" is not defined.')) {
                return $this->markTestSkipped('The self-update command is not yet released.');
            }

            echo $process->getErrorOutput();
        }

        $this->assertTrue($process->isSuccessful());

        return $this->assertContains("manalize is already up to date ($latestTag)", $process->getOutput());
    }

    public function tearDown()
    {
        (new Filesystem())->remove(self::$cwd);
    }

    public static function tearDownAfterClass()
    {
        (new Filesystem())->remove(MANALIZE_TMP_ROOT_DIR);
    }
}
