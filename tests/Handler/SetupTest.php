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

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Dumper;
use Manala\Manalize\Env\EnvName;
use Manala\Manalize\Handler\Setup;
use Symfony\Component\Filesystem\Filesystem;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    private static $cwd;

    public static function setUpBeforeClass()
    {
        self::$cwd = manala_get_tmp_dir('tests_setup_handler_');
    }

    public function provideHandleData()
    {
        return [
            [
                [],
                [
                    'Vagrantfile',
                    'ansible/.manalize.yml',
                    'ansible/ansible.yml',
                    'ansible/app.yml',
                    'ansible/deploy.yml',
                    'ansible/group_vars/app.yml',
                    'ansible/group_vars/app_local.yml.sample',
                    'ansible/group_vars/deploy.yml',
                    'ansible/group_vars/deploy_demo.yml',
                    'ansible/group_vars/deploy_prod.yml',
                    'Makefile',
                ],
            ],
            [
                ['dumper_flags' => Dumper::DUMP_METADATA],
                ['ansible/.manalize.yml'],
            ],
        ];
    }

    /**
     * @dataProvider provideHandleData
     */
    public function testHandle($options, $expectedFiles)
    {
        $dumpedFiles = [];

        $handler = new Setup(
            self::$cwd,
            new AppName('setup_test'),
            EnvName::SYMFONY(),
            $this->prophesize(\Iterator::class)->reveal(),
            $options
        );

        $handler->handle(function ($target) use (&$dumpedFiles) {
            $dumpedFiles[] = $target;
        });

        foreach ($expectedFiles as $filename) {
            $this->assertContains($filename, $dumpedFiles);
        }
    }

    public static function tearDownAfterClass()
    {
        (new Filesystem())->remove(MANALIZE_TMP_ROOT_DIR);
    }
}
