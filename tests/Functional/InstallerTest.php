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

use Symfony\Component\Process\Process;

class InstallerTest extends TestCase
{
    public function testInstaller()
    {
        $targetDir = manala_get_tmp_dir('tests_installer_');

        $process = new Process("php installer.php $targetDir", MANALIZE_DIR);
        $process->run();

        if (!$process->isSuccessful()) {
            echo $process->getErrorOutput();

            $this->fail();
        }

        $expectedPath = "$targetDir/manalize";
        $this->assertStringMatchesFormat(<<<TXT

Manalize Installer
==================

- Retrieving the latest release...
- Downloading the phar for v0.%d.%d...
- Moving executable...
- Making Manalize executable...

✔ Manalize successfully installed at path "$expectedPath"

TXT
            , $process->getOutput());

        $this->assertFileExists($expectedPath);
        $this->assertSame('0755', substr(decoct(fileperms($expectedPath)), -4));
    }
}
