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

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Package;
use Manala\Manalize\Env\Manifest\ManifestParser;
use Manala\Manalize\Env\TemplateName;
use Manala\Manalize\Handler\Setup;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * TestCase.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    const FIXTURES_DIR = __DIR__.'/../fixtures';

    private static $symfonyStandardCopyPath = null;

    public static function tearDownAfterClass()
    {
        self::$symfonyStandardCopyPath = null;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        (new Filesystem())->remove(MANALIZE_TMP_ROOT_DIR);
    }

    protected static function getDefaultPackagesForEnv(TemplateName $envType)
    {
        foreach (ManifestParser::parse($envType)->get('packages') as $name => $configs) {
            if (isset($configs['constraint'])) {
                yield new Package($name, $configs['enabled'], $configs['default']);

                continue;
            }

            yield new Package($name, $configs['enabled']);
        }
    }

    protected static function updatePackageVersion(\Traversable $packages, $name, $version)
    {
        foreach ($packages as $package) {
            if ($package->getName() === $name) {
                yield new Package($name, $package->isEnabled(), $version);

                continue;
            }

            yield $package;
        }
    }

    protected static function enablePackageWithVersion(\Traversable $packages, $name, $version = null)
    {
        foreach ($packages as $package) {
            if ($package->getName() === $name) {
                (\Closure::bind(function () use ($version) {
                    $this->enabled = true;
                    $this->version = $version;
                }, $package, Package::class))();
            }

            yield $package;
        }
    }

    protected static function enablePackage(\Traversable $packages, $name)
    {
        foreach ($packages as $package) {
            if ($package->getName() === $name) {
                (\Closure::bind(function () {
                    $this->enabled = true;
                }, $package, Package::class))();
            }

            yield $package;
        }
    }

    protected static function createSymfonyStandardProject($cwd)
    {
        if (null === self::$symfonyStandardCopyPath) {
            self::$symfonyStandardCopyPath = manala_get_tmp_dir('test_case_symfony_standard_app_');

            (new Process('composer create-project symfony/framework-standard-edition:3.1.* . --no-install --no-progress --no-interaction', self::$symfonyStandardCopyPath))
                ->setTimeout(null)
                ->run();
        }

        (new Filesystem())->mirror(self::$symfonyStandardCopyPath, $cwd);
    }

    protected static function manalizeProject($cwd, $appName, TemplateName $envType, \Traversable $packages = null)
    {
        if (null === $packages) {
            $packages = Setup::createDefaultPackageSet(ManifestParser::parse($envType));
        }

        (new Setup($cwd, new AppName($appName), $envType, $packages))->handle(function () {
        });
    }

    protected static function createManalizedProject($cwd, $appName = 'dummy.manala', TemplateName $envType = null, \Iterator $packages = null)
    {
        self::createSymfonyStandardProject($cwd);

        if (null === $envType) {
            $envType = TemplateName::ELAO_SYMFONY();
        }

        self::manalizeProject($cwd, $appName, $envType, $packages);
    }
}
