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
use Manala\Manalize\Env\TemplateName;
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
                    'manala/Vagrantfile',
                    'ansible/ansible.yaml',
                    'ansible/app.yaml',
                    'ansible/deploy.yaml',
                    'ansible/group_vars/app.yaml',
                    'ansible/group_vars/app_local.yaml.sample',
                    'ansible/group_vars/deploy.yaml',
                    'ansible/group_vars/deploy_demo.yaml',
                    'ansible/group_vars/deploy_prod.yaml',
                    'Makefile',
                    'manala/make/Makefile.vm',
                    'manala.yaml',
                ],
            ],
            [
                ['dumper_flags' => Dumper::DUMP_MANALA],
                ['manala.yaml'],
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
            TemplateName::ELAO_SYMFONY(),
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
