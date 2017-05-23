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

use Manala\Manalize\Command\Diff;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class DiffTest extends TestCase
{
    const EXPECTED_PATCH_FILE = MANALIZE_DIR.'/tests/fixtures/Command/DiffTest/expected.patch';

    private static $cwd;

    public static function setUpBeforeClass()
    {
        $cwd = manala_get_tmp_dir('tests_diff_');
        $fs = new Filesystem();

        self::createManalizedProject($cwd);

        // Tweak project files:
        $fs->remove($cwd.'/ansible/deploy.yaml');
        file_put_contents($cwd.'/Makefile', " \n This line is expected in the patch", FILE_APPEND);

        self::$cwd = $cwd;
    }

    public function testExecute()
    {
        $tester = new CommandTester(new Diff());
        $tester->execute(['cwd' => static::$cwd, '--env' => 'elao-symfony']);

        if (0 !== $tester->getStatusCode()) {
            echo $tester->getDisplay();
        }

        $this->assertSame(0, $tester->getStatusCode());

        UPDATE_FIXTURES ? file_put_contents(static::EXPECTED_PATCH_FILE, $tester->getDisplay(true)) : null;

        $this->assertStringEqualsFile(static::EXPECTED_PATCH_FILE, $tester->getDisplay(true));
    }
}
