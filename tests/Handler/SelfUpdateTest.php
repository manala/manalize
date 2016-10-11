<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Handler;

use Manala\Manalize\Application;
use Manala\Manalize\Handler\SelfUpdate;
use Manala\Manalize\Handler\Setup;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class SelfUpdateTest extends \PHPUnit_Framework_TestCase
{
    private static $cwd;

    public function setUp()
    {
        $cwd = manala_get_tmp_dir('tests_selfupdate_handler_');
        $fs = new Filesystem();

        if ($fs->exists($cwd)) {
            $fs->remove($cwd);
        }

        $fs->mkdir($cwd);

        self::$cwd = $cwd;

        $process = new Process('make build', MANALIZE_DIR);
        $process->run();

        if (!$process->isSuccessful() && false !== strpos($process->getErrorOutput(), 'box: command not found')) {
            $this->markTestSkipped('"kherge/box" is required and should be in your $PATH in order to build the phar.');
        }

        chmod(MANALIZE_DIR.'/manalize.phar', 0777);
        rename(MANALIZE_DIR.'/manalize.phar', self::$cwd.'/manalize.phar');
    }

    public function testHandle()
    {
        $handler = new SelfUpdate(self::$cwd.'/manalize.phar');
        $this->assertTrue($handler->handle());
    }

    public function testGetLatestTag()
    {
        $releaseUri = sprintf('https://api.github.com/repos/%s/releases/latest', Application::REPOSITORY_NAME);
        $latestBuild = json_decode(
            file_get_contents($releaseUri, null, stream_context_create(['http' => ['header' => 'User-Agent: '.Application::REPOSITORY_NAME]])),
            true
        );

        $this->assertSame($latestBuild['tag_name'], (new SelfUpdate(self::$cwd.'/manalize.phar'))->getLatestTag());
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
