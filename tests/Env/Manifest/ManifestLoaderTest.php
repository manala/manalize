<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env\Manifest;

use Manala\Manalize\Env\Manifest\Manifest;
use Manala\Manalize\Env\Manifest\ManifestLoader;
use Symfony\Component\Filesystem\Filesystem;

class ManifestLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $tmpDir;

    /**
     * @dataProvider getManifests()
     */
    public function testLoad($raw, $expectedManifest)
    {
        file_put_contents($file = $this->tmpDir.'/manifest.yaml', $raw);

        $this->assertEquals((new ManifestLoader())->load($file), $expectedManifest);
    }

    public function getManifests()
    {
        yield [
            '{ system: { php: 7.1 } }',
            new Manifest(['system' => ['php' => '7.1']]),
        ];


    }

    protected function setup()
    {
        $this->tmpDir = manala_get_tmp_dir('manifest_loader');
    }

    protected function tearDown()
    {
        (new Filesystem())->remove($this->tmpDir);
    }
}
